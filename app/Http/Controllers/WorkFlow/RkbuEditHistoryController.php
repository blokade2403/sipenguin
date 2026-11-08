<?php

namespace App\Http\Controllers\WorkFlow;

use App\Http\Controllers\Controller;
use App\Models\Rkbu\RkbuEditHistory;
use App\Support\RkbuEditHistoryPresenter;

class RkbuEditHistoryController extends Controller
{
    public function index()
    {
        $history = RkbuEditHistory::query()
            ->with(['user', 'rkbu'])
            ->where('fase_urutan', '>', 1)
            ->latest()
            ->paginate(25)
            ->through(function ($entry) {
                $entry->comparisonRows = RkbuEditHistoryPresenter::comparisonRows(
                    ['header' => $entry->header_sebelum, 'details' => $entry->detail_sebelum],
                    ['header' => $entry->header_sesudah, 'details' => $entry->detail_sesudah]
                );
                return $entry;
            });

        return view('workflow.history.index', compact('history'));
    }
}
