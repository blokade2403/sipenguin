<?php

namespace App\Http\Controllers\UsulanBelanja;

use App\Http\Controllers\Controller;
use App\Models\UsulanBelanja\UsulanBelanjaEditHistory;

class UsulanBelanjaHistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_unless($user?->hasRole('Super Admin') || $user?->hasRole('Administrator'), 403);

        $history = UsulanBelanjaEditHistory::query()
            ->with(['user', 'usulanBelanja'])
            ->whereHas('usulanBelanja', fn ($query) => $query->where('tahun_anggaran_id', session('tahun_anggaran_id')))
            ->latest()
            ->paginate(30);

        return view('usulan_belanja.history', compact('history'));
    }
}
