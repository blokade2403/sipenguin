<?php

namespace App\Http\Controllers\MasterBackend\SettingInput;

use App\Http\Controllers\Controller;
use App\Models\MasterBackend\SettingInput\PaguSubkategoriRkbu;
use App\Models\MasterBackend\SettingInput\PaguAnggaran;
use App\Models\MasterBackend\SettingInput\TahunAnggaran;
use App\Models\MasterBackend\SettingRkbu\SubKategoriRkbu;
use App\Models\Rkbu\Rkbu;
use App\Services\Workflow\PaguAnggaranService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaguSubkategoriRkbuController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            abort_unless(auth()->user()?->hasRole('Administrator'), 403);
            return $next($request);
        });
    }

    public function index(Request $request, PaguAnggaranService $service)
    {
        $years = TahunAnggaran::orderByDesc('nama_tahun_anggaran')->get();
        $yearId = $request->input('tahun_anggaran_id') ?: session('tahun_anggaran_id');
        if (! $years->contains('id', $yearId)) $yearId = $years->firstWhere('status', 'aktif')?->id ?? $years->first()?->id;
        $subPaguBySource = PaguSubkategoriRkbu::query()->where('tahun_anggaran_id', $yearId)
            ->selectRaw('jenis_sumber_dana, SUM(jumlah_pagu) as total_pagu, COUNT(*) as jumlah_input')
            ->groupBy('jenis_sumber_dana')->get()->keyBy('jenis_sumber_dana');
        $anggaranPaguBySource = PaguAnggaran::query()->where('tahun_anggaran_id', $yearId)
            ->selectRaw('jenis_sumber_dana, SUM(jumlah_pagu) as total_pagu')
            ->groupBy('jenis_sumber_dana')->get()->keyBy('jenis_sumber_dana');
        $summaryBySource = collect(['BLUD', 'Non BLUD'])->mapWithKeys(function ($source) use ($subPaguBySource, $anggaranPaguBySource) {
            $paguInput = (float) ($subPaguBySource->get($source)?->total_pagu ?? 0);
            $paguAnggaran = (float) ($anggaranPaguBySource->get($source)?->total_pagu ?? 0);
            return [$source => [
                'pagu_anggaran' => $paguAnggaran,
                'pagu_subkategori' => $paguInput,
                'selisih' => $paguAnggaran - $paguInput,
                'jumlah_input' => (int) ($subPaguBySource->get($source)?->jumlah_input ?? 0),
            ]];
        });
        $summary = [
            'pagu_anggaran' => $summaryBySource->sum('pagu_anggaran'),
            'pagu_subkategori' => $summaryBySource->sum('pagu_subkategori'),
            'selisih' => $summaryBySource->sum('selisih'),
            'jumlah_input' => $summaryBySource->sum('jumlah_input'),
        ];
        $rows = SubKategoriRkbu::with('kategori')->orderBy('kode_sub_kategori_rkbu')->get()->flatMap(function ($sub) use ($yearId, $service) {
            return collect(['BLUD', 'Non BLUD'])->map(function ($source) use ($sub, $yearId, $service) {
                $setting = PaguSubkategoriRkbu::where('tahun_anggaran_id', $yearId)->where('sub_kategori_rkbu_id', $sub->id_sub_kategori_rkbu)->where('jenis_sumber_dana', $source)->first();
                $total = Rkbu::where('tahun_anggaran_id', $yearId)->where('sub_kategori_rkbu_id', $sub->id_sub_kategori_rkbu)->whereNotIn('status', ['rejected'])->with(['sumberDana', 'subKegiatan.sumber_danas'])->get()->filter(fn ($rkbu) => $service->rkbuMatchesFundingSource($rkbu, $source))->sum(fn ($rkbu) => $service->detailTotal($rkbu));
                return (object) ['sub' => $sub, 'source' => $source, 'pagu' => $setting, 'used' => $total];
            });
        });
        return view('master_backend.setting_input.pagu_subkategori.index', compact('years', 'yearId', 'rows', 'summary', 'summaryBySource'));
    }

    public function create(Request $request)
    {
        return $this->form(new PaguSubkategoriRkbu([
            'tahun_anggaran_id' => $request->query('tahun_anggaran_id', session('tahun_anggaran_id')),
            'sub_kategori_rkbu_id' => $request->query('sub_kategori_rkbu_id'),
            'jenis_sumber_dana' => $request->query('jenis_sumber_dana'),
        ]), 'store');
    }

    public function store(Request $request)
    {
        PaguSubkategoriRkbu::create($this->validated($request));
        return redirect()->route('pagu_subkategori.index', ['tahun_anggaran_id' => $request->tahun_anggaran_id])->with('success', 'Pagu subkategori berhasil ditambahkan.');
    }

    public function edit(PaguSubkategoriRkbu $paguSubkategori)
    {
        return $this->form($paguSubkategori, 'update');
    }

    public function update(Request $request, PaguSubkategoriRkbu $paguSubkategori)
    {
        $paguSubkategori->update($this->validated($request, $paguSubkategori));
        return redirect()->route('pagu_subkategori.index', ['tahun_anggaran_id' => $paguSubkategori->tahun_anggaran_id])->with('success', 'Pagu subkategori berhasil diperbarui.');
    }

    public function destroy(PaguSubkategoriRkbu $paguSubkategori)
    {
        $yearId = $paguSubkategori->tahun_anggaran_id;
        $paguSubkategori->delete();
        return redirect()->route('pagu_subkategori.index', ['tahun_anggaran_id' => $yearId])->with('success', 'Pagu subkategori berhasil dihapus.');
    }

    private function form(PaguSubkategoriRkbu $pagu, string $mode)
    {
        return view('master_backend.setting_input.pagu_subkategori.form', [
            'pagu' => $pagu,
            'years' => TahunAnggaran::orderByDesc('nama_tahun_anggaran')->get(),
            'subkategoris' => SubKategoriRkbu::orderBy('kode_sub_kategori_rkbu')->get(),
            'mode' => $mode,
        ]);
    }

    private function validated(Request $request, ?PaguSubkategoriRkbu $pagu = null): array
    {
        return $request->validate([
            'tahun_anggaran_id' => ['required', 'uuid', 'exists:tahun_anggarans,id'],
            'sub_kategori_rkbu_id' => ['required', 'uuid', 'exists:sub_kategori_rkbus,id_sub_kategori_rkbu', Rule::unique('pagu_subkategori_rkbus', 'sub_kategori_rkbu_id')->where('tahun_anggaran_id', $request->input('tahun_anggaran_id'))->where('jenis_sumber_dana', $request->input('jenis_sumber_dana'))->ignore($pagu?->id)],
            'jenis_sumber_dana' => ['required', 'in:BLUD,Non BLUD'],
            'jumlah_pagu' => ['required', 'numeric', 'min:0', 'max:9999999999999999.99'],
        ]);
    }
}
