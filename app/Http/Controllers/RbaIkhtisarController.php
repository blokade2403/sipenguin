<?php

namespace App\Http\Controllers;

use App\Models\MasterBackend\SettingInput\TahunAnggaran;
use App\Models\MasterBackend\SettingRkbu\SubKategoriRkbu;
use App\Models\RbaIkhtisar\RbaIkhtisarItem;
use App\Models\RbaIkhtisar\RbaIkhtisarMapping;
use App\Models\RbaIkhtisar\RbaIkhtisarRealisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RbaIkhtisarController extends Controller
{
    private const EXPENSE_SECTIONS = ['belanja_operasi', 'belanja_modal'];
    private const INCOME_SECTIONS = ['pendapatan', 'penerimaan_pembiayaan', 'pengeluaran_pembiayaan'];

    public function index(Request $request)
    {
        $years = TahunAnggaran::with('fase')->orderByDesc('nama_tahun_anggaran')->get();
        $yearId = $request->input('tahun_anggaran_id') ?: session('tahun_anggaran_id');
        if (! $years->contains('id', $yearId)) {
            $yearId = $years->firstWhere('status', 'aktif')?->id ?? $years->first()?->id;
        }
        $year = $years->firstWhere('id', $yearId);
        $realizationYearId = $request->input('realisasi_tahun_anggaran_id') ?: $this->previousYear($years, $year);
        if (! $years->contains('id', $realizationYearId)) {
            $realizationYearId = $this->previousYear($years, $year);
        }
        $realizationYear = $years->firstWhere('id', $realizationYearId);

        $items = RbaIkhtisarItem::query()->where('is_active', true)
            ->with('mappings')
            ->orderBy('urutan')->get();

        $subcategoryIds = $items->flatMap(fn ($item) => $item->mappings->pluck('sub_kategori_rkbu_id'))->filter()->unique();
        $incomeAccountIds = $items->flatMap(fn ($item) => $item->mappings->pluck('rekening_pendapatan_id'))->filter()->unique();

        $paguBySource = collect();
        if ($yearId && $subcategoryIds->isNotEmpty()) {
            $paguBySource = DB::table('pagu_subkategori_rkbus')
                ->where('tahun_anggaran_id', $yearId)
                ->whereIn('sub_kategori_rkbu_id', $subcategoryIds)
                ->get(['sub_kategori_rkbu_id', 'jenis_sumber_dana', 'jumlah_pagu'])
                ->keyBy(fn ($row) => $row->sub_kategori_rkbu_id . '|' . $row->jenis_sumber_dana);
        }

        $targetByAccount = collect();
        if ($yearId && $incomeAccountIds->isNotEmpty()) {
            $targetByAccount = DB::table('target_pendapatans')
                ->where('tahun_anggaran_id', $yearId)
                ->whereIn('rekening_pendapatan_id', $incomeAccountIds)
                ->select('rekening_pendapatan_id', DB::raw('SUM(target_tahunan) as jumlah'))
                ->groupBy('rekening_pendapatan_id')->pluck('jumlah', 'rekening_pendapatan_id');
        }

        $historicalByItem = $realizationYearId
            ? RbaIkhtisarRealisasi::query()->where('tahun_anggaran_id', $realizationYearId)
                ->get()->keyBy(fn ($value) => $value->ikhtisar_item_id . '|' . $value->jenis_sumber_dana)
            : collect();

        $rows = $items->mapWithKeys(function ($item) use ($paguBySource, $targetByAccount, $historicalByItem) {
            $values = ['BLUD' => 0.0, 'Non BLUD' => 0.0];
            foreach ($item->mappings as $mapping) {
                if ($mapping->sub_kategori_rkbu_id) {
                    foreach (['BLUD', 'Non BLUD'] as $source) {
                        if ($mapping->jenis_sumber_dana && $mapping->jenis_sumber_dana !== $source) continue;
                        $pagu = $paguBySource->get($mapping->sub_kategori_rkbu_id . '|' . $source);
                        $values[$source] += (float) ($pagu->jumlah_pagu ?? 0);
                    }
                } elseif ($mapping->rekening_pendapatan_id && $mapping->jenis_sumber_dana) {
                    $values[$mapping->jenis_sumber_dana] += (float) ($targetByAccount->get($mapping->rekening_pendapatan_id) ?? 0);
                }
            }

            return [$item->id => [
                'BLUD' => $values['BLUD'],
                'Non BLUD' => $values['Non BLUD'],
                'realisasi_BLUD' => (float) ($historicalByItem->get($item->id . '|BLUD')?->jumlah_realisasi ?? 0),
                'realisasi_Non BLUD' => (float) ($historicalByItem->get($item->id . '|Non BLUD')?->jumlah_realisasi ?? 0),
            ]];
        })->all();

        $this->rollUpDetailRows($items, $rows);
        $this->calculateTotals($items, $rows);

        $reportRows = $items->map(function ($item) use ($rows) {
            $values = $rows[$item->id];
            $plan = $values['BLUD'] + $values['Non BLUD'];
            $realized = $values['realisasi_BLUD'] + $values['realisasi_Non BLUD'];
            $difference = $realized - $plan;

            return [
                'item' => $item,
                'values' => $values,
                'jumlah' => $plan,
                'realisasi' => $realized,
                'selisih' => $difference,
                'persentase' => $realized != 0.0 ? $difference / $realized * 100 : null,
            ];
        });

        $unmappedCount = $items->filter(fn ($item) => $item->jenis_baris === 'detail' && $item->section !== 'ringkasan'
            && $item->mappings->isEmpty()
            && ! $items->contains(fn ($candidate) => $candidate->section === $item->section
                && $candidate->jenis_baris === 'detail' && $candidate->level > $item->level
                && $candidate->urutan > $item->urutan))->count();

        return view('rba_ikhtisar.index', compact(
            'years', 'year', 'yearId', 'realizationYear', 'realizationYearId', 'reportRows', 'unmappedCount'
        ));
    }

    public function mappings(Request $request)
    {
        $this->authorizeSettings();
        $items = RbaIkhtisarItem::query()->where('jenis_baris', 'detail')->where('section', '!=', 'ringkasan')
            ->with('mappings.subkategori')->orderBy('urutan')->get();
        return view('rba_ikhtisar.mappings', compact('items'));
    }

    public function editMapping(RbaIkhtisarItem $item)
    {
        $this->authorizeSettings();
        abort_unless($item->jenis_baris === 'detail' && $item->section !== 'ringkasan', 404);
        $item->load('mappings.subkategori');
        $subcategories = SubKategoriRkbu::query()->where('status', 'aktif')->orderBy('kode_sub_kategori_rkbu')->get();
        $incomeAccounts = DB::table('rekening_pendapatans')->where('aktif', true)->orderBy('kode_rekening')->get(['id', 'kode_rekening', 'nama_rekening']);
        $years = TahunAnggaran::orderByDesc('nama_tahun_anggaran')->get();
        $year = $years->firstWhere('status', 'aktif') ?? $years->first();
        $historyYearId = $this->previousYear($years, $year) ?? $year?->id;
        $historyValues = $historyYearId
            ? $item->realisasis()->where('tahun_anggaran_id', $historyYearId)->get()->keyBy('jenis_sumber_dana')
            : collect();

        return view('rba_ikhtisar.mapping_edit', compact('item', 'subcategories', 'incomeAccounts', 'years', 'historyYearId', 'historyValues'));
    }

    public function updateMappings(Request $request, RbaIkhtisarItem $item)
    {
        $this->authorizeSettings();
        $isExpense = in_array($item->section, self::EXPENSE_SECTIONS, true);
        if ($isExpense) {
            $validated = $request->validate([
                'sub_kategori_rkbu_ids' => ['nullable', 'array'],
                'sub_kategori_rkbu_ids.*' => ['uuid', 'exists:sub_kategori_rkbus,id_sub_kategori_rkbu'],
            ]);
            $item->mappings()->delete();
            foreach (array_unique($validated['sub_kategori_rkbu_ids'] ?? []) as $id) {
                $item->mappings()->create(['sub_kategori_rkbu_id' => $id]);
            }
        } else {
            $validated = $request->validate([
                'jenis_sumber_dana' => ['required', Rule::in(['BLUD', 'Non BLUD'])],
                'rekening_pendapatan_ids' => ['nullable', 'array'],
                'rekening_pendapatan_ids.*' => ['uuid', 'exists:rekening_pendapatans,id'],
            ]);
            $item->mappings()->delete();
            foreach (array_unique($validated['rekening_pendapatan_ids'] ?? []) as $id) {
                $item->mappings()->create([
                    'rekening_pendapatan_id' => $id,
                    'jenis_sumber_dana' => $validated['jenis_sumber_dana'],
                ]);
            }
        }

        return redirect()->route('rba-ikhtisar.mapping.edit', $item)->with('success', 'Pemetaan Ikhtisar berhasil disimpan.');
    }

    public function saveRealisasi(Request $request, RbaIkhtisarItem $item)
    {
        $this->authorizeSettings();
        $validated = $request->validate([
            'tahun_anggaran_id' => ['required', 'uuid', 'exists:tahun_anggarans,id'],
            'realisasi_blud' => ['required', 'numeric', 'min:0', 'max:9999999999999999.99'],
            'realisasi_non_blud' => ['required', 'numeric', 'min:0', 'max:9999999999999999.99'],
            'keterangan_sumber' => ['nullable', 'string', 'max:2000'],
        ]);

        foreach (['BLUD' => 'realisasi_blud', 'Non BLUD' => 'realisasi_non_blud'] as $source => $field) {
            RbaIkhtisarRealisasi::updateOrCreate(
                ['tahun_anggaran_id' => $validated['tahun_anggaran_id'], 'ikhtisar_item_id' => $item->id, 'jenis_sumber_dana' => $source],
                ['jumlah_realisasi' => $validated[$field], 'keterangan_sumber' => $validated['keterangan_sumber'] ?? null, 'created_by' => auth()->id()]
            );
        }

        return redirect()->route('rba-ikhtisar.mapping.edit', $item)->with('success', 'Nilai realisasi historis berhasil disimpan.');
    }

    private function previousYear($years, ?TahunAnggaran $year): ?string
    {
        if (! $year) return null;
        preg_match('/\d{4}/', (string) $year->nama_tahun_anggaran, $currentMatch);
        $current = isset($currentMatch[0]) ? (int) $currentMatch[0] : null;
        if (! $current) return $years->firstWhere('id', '!=', $year->id)?->id;

        return $years->first(function ($candidate) use ($current) {
            preg_match('/\d{4}/', (string) $candidate->nama_tahun_anggaran, $match);
            return isset($match[0]) && (int) $match[0] === $current - 1;
        })?->id;
    }

    private function rollUpDetailRows($items, &$rows): void
    {
        foreach ($items->groupBy('section') as $sectionItems) {
            $stack = [];
            $children = [];
            foreach ($sectionItems->where('jenis_baris', 'detail') as $item) {
                while ($stack && end($stack)->level >= $item->level) array_pop($stack);
                if ($stack) $children[end($stack)->id][] = $item->id;
                $stack[] = $item;
            }

            foreach ($sectionItems->where('jenis_baris', 'detail')->reverse() as $item) {
                if ($item->mappings->isNotEmpty() || empty($children[$item->id])) continue;
                foreach ($children[$item->id] as $childId) {
                    foreach (['BLUD', 'Non BLUD', 'realisasi_BLUD', 'realisasi_Non BLUD'] as $key) {
                        $rows[$item->id][$key] += $rows[$childId][$key];
                    }
                }
            }
        }
    }

    private function calculateTotals($items, &$rows): void
    {
        $sectionTotals = [];
        foreach (['pendapatan', 'belanja_operasi', 'belanja_modal', 'penerimaan_pembiayaan', 'pengeluaran_pembiayaan'] as $section) {
            $sectionTotals[$section] = ['BLUD' => 0.0, 'Non BLUD' => 0.0, 'realisasi_BLUD' => 0.0, 'realisasi_Non BLUD' => 0.0];
            foreach ($items->where('section', $section)->where('jenis_baris', 'detail') as $item) {
                if ($item->level === 0) {
                    foreach (array_keys($sectionTotals[$section]) as $key) $sectionTotals[$section][$key] += $rows[$item->id][$key];
                }
            }
        }

        foreach ($items as $item) {
            if ($item->jenis_baris === 'header') continue;
            $name = strtolower($item->nama_ikhtisar);
            $values = null;
            if (str_contains($name, 'total pendapatan') || str_contains($name, 'jumlah total pendapatan')) {
                $values = $sectionTotals['pendapatan'];
            } elseif (str_contains($name, 'total belanja (') || str_contains($name, 'jumlah total belanja')) {
                $values = $this->addValues($sectionTotals['belanja_operasi'], $sectionTotals['belanja_modal']);
            } elseif (str_contains($name, 'belanja operasi')) {
                $values = $sectionTotals['belanja_operasi'];
            } elseif (str_contains($name, 'belanja modal')) {
                $values = $sectionTotals['belanja_modal'];
            } elseif (str_contains($name, 'surplus/defisit')) {
                $values = $this->subtractValues($sectionTotals['pendapatan'], $this->addValues($sectionTotals['belanja_operasi'], $sectionTotals['belanja_modal']));
            } elseif (str_contains($name, 'jumlah penerimaan pembiayaan')) {
                $values = $sectionTotals['penerimaan_pembiayaan'];
            } elseif (str_contains($name, 'jumlah pengeluaran pembiayaan')) {
                $values = $sectionTotals['pengeluaran_pembiayaan'];
            }
            if ($values) $rows[$item->id] = $values;
        }
    }

    private function addValues(array $left, array $right): array
    {
        foreach ($left as $key => $value) $left[$key] += $right[$key];
        return $left;
    }

    private function subtractValues(array $left, array $right): array
    {
        foreach ($left as $key => $value) $left[$key] -= $right[$key];
        return $left;
    }

    private function authorizeSettings(): void
    {
        abort_unless(auth()->user()?->hasRole('Administrator') || auth()->user()?->hasRole('Super Admin'), 403);
    }
}
