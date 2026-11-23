<?php

namespace App\Http\Controllers\WorkFlow;

use App\Http\Controllers\Controller;
use App\Models\Rkbu\RkbuEditHistory;
use App\Support\RkbuEditHistoryPresenter;

class RkbuEditHistoryController extends Controller
{
    private const MODULES = [
        'barjas' => ['label' => 'RKBU Barjas', 'relation' => 'detailBarjas'],
        'persediaan' => ['label' => 'RKBU Persediaan', 'relation' => 'detailPersediaan'],
        'modal-kantor' => ['label' => 'RKBU Modal Kantor', 'relation' => 'detailModal'],
        'modal-alkes' => ['label' => 'RKBU Modal Alkes', 'relation' => 'detailModalAlkes'],
        'pegawai' => ['label' => 'RKBU Pegawai', 'relation' => 'detailPegawai'],
    ];

    public function index(?string $module = null)
    {
        abort_unless(auth()->user()?->hasRole('Super Admin') || auth()->user()?->hasRole('Administrator'), 403);
        abort_if($module !== null && ! isset(self::MODULES[$module]), 404);

        $query = RkbuEditHistory::query()
            ->with(['user', 'rkbu.detailBarjas', 'rkbu.detailPersediaan', 'rkbu.detailModal', 'rkbu.detailModalAlkes', 'rkbu.detailPegawai'])
            ->whereHas('rkbu', fn ($query) => $query->where('tahun_anggaran_id', session('tahun_anggaran_id')));
        if ($module !== null) {
            $query->whereHas('rkbu.' . self::MODULES[$module]['relation']);
        }

        $history = $query->latest()->paginate(25)
            ->through(function ($entry) {
                $entry->comparisonRows = RkbuEditHistoryPresenter::comparisonRows(
                    ['header' => $entry->header_sebelum, 'details' => $entry->detail_sebelum],
                    ['header' => $entry->header_sesudah, 'details' => $entry->detail_sesudah]
                );
                return $entry;
            });

        $modules = self::MODULES;
        $title = $module === null ? 'Semua Modul RKBU' : self::MODULES[$module]['label'];

        return view('workflow.history.index', compact('history', 'modules', 'module', 'title'));
    }
}
