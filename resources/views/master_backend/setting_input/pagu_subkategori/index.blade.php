@extends('layouts.main')

@section('content')
    @php
        $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-6">
        <div>
            <h1 class="fs-2 fw-bold text-gray-900 mb-1">Pagu Subkategori RKBU</h1>
            <div class="text-muted">Bandingkan total pagu anggaran dengan pagu subkategori yang sudah diinput untuk setiap sumber dana.</div>
        </div>
        <a href="{{ route('pagu_subkategori.create', ['tahun_anggaran_id' => $yearId]) }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Pagu</a>
    </div>

    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="card border-0 shadow-sm mb-6">
        <div class="card-body d-flex flex-wrap align-items-end justify-content-between gap-4">
            <form method="GET" action="{{ route('pagu_subkategori.index') }}">
                <label for="tahun_anggaran_id" class="form-label fw-semibold">Tahun anggaran</label>
                <select id="tahun_anggaran_id" name="tahun_anggaran_id" class="form-select" style="min-width:260px" onchange="this.form.submit()">
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}" @selected($year->id === $yearId)>{{ $year->nama_tahun_anggaran }}{{ $year->status === 'aktif' ? ' · Aktif' : '' }}</option>
                    @endforeach
                </select>
            </form>
            <div class="text-muted">Selisih = total pagu anggaran − total pagu subkategori yang sudah diinput.</div>
        </div>
    </div>

    <div class="row g-5 mb-6">
        @foreach ([
            ['label' => 'Total Pagu Anggaran', 'value' => $summary['pagu_anggaran'], 'color' => 'primary', 'icon' => 'fa-wallet'],
            ['label' => 'Total Pagu Subkategori Terinput', 'value' => $summary['pagu_subkategori'], 'color' => 'info', 'icon' => 'fa-list-check'],
            ['label' => 'Sisa Pagu Belum Dialokasikan', 'value' => $summary['selisih'], 'color' => $summary['selisih'] < 0 ? 'danger' : 'success', 'icon' => 'fa-scale-balanced'],
        ] as $item)
            <div class="col-xl-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm"><div class="card-body d-flex align-items-center gap-4">
                    <span class="pagu-summary-icon bg-light-{{ $item['color'] }} text-{{ $item['color'] }}"><i class="fas {{ $item['icon'] }}"></i></span>
                    <div><div class="text-muted fw-semibold mb-1">{{ $item['label'] }}</div><div class="fs-3 fw-bold text-gray-900">{{ $rupiah($item['value']) }}</div></div>
                </div></div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mb-6">
        <div class="card-header border-0 pt-6"><div class="card-title"><h2 class="fw-bold mb-0">Ringkasan per Sumber Dana</h2></div></div>
        <div class="card-body pt-2"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-3 mb-0">
            <thead><tr class="text-muted fw-bold text-uppercase fs-7"><th>Sumber dana</th><th class="text-end">Pagu anggaran</th><th class="text-end">Pagu subkategori terinput</th><th class="text-end">Selisih</th><th class="text-end">Jumlah subkategori terisi</th></tr></thead>
            <tbody>
                @foreach ($summaryBySource as $source => $totals)
                    <tr><td><span class="badge badge-light-{{ $source === 'BLUD' ? 'primary' : 'warning' }}">{{ $source }}</span></td><td class="text-end">{{ $rupiah($totals['pagu_anggaran']) }}</td><td class="text-end">{{ $rupiah($totals['pagu_subkategori']) }}</td><td class="text-end fw-bold {{ $totals['selisih'] < 0 ? 'text-danger' : 'text-success' }}">{{ $rupiah($totals['selisih']) }}</td><td class="text-end">{{ number_format($totals['jumlah_input'], 0, ',', '.') }}</td></tr>
                @endforeach
                <tr class="fw-bold bg-light"><td>Total</td><td class="text-end">{{ $rupiah($summary['pagu_anggaran']) }}</td><td class="text-end">{{ $rupiah($summary['pagu_subkategori']) }}</td><td class="text-end {{ $summary['selisih'] < 0 ? 'text-danger' : 'text-success' }}">{{ $rupiah($summary['selisih']) }}</td><td class="text-end">{{ number_format($summary['jumlah_input'], 0, ',', '.') }}</td></tr>
            </tbody>
        </table></div></div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 pt-6 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div><h2 class="fw-bold text-gray-900 mb-1">Daftar Pagu Subkategori</h2><span class="text-muted fs-7">Data dipisahkan berdasarkan sumber dana · terpakai menghitung total RKBU yang belum ditolak</span></div>
            <input id="filter-subkategori" type="search" class="form-control" style="max-width:320px" placeholder="Cari kode, subkategori, atau sumber dana" aria-label="Cari pagu subkategori">
        </div>
        <div class="card-body pt-2">
            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" id="tab-blud" data-bs-toggle="tab" data-bs-target="#panel-blud" type="button" role="tab" aria-controls="panel-blud" aria-selected="true">BLUD <span class="badge badge-light-primary ms-2">{{ $rows->where('source', 'BLUD')->count() }}</span></button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" id="tab-non-blud" data-bs-toggle="tab" data-bs-target="#panel-non-blud" type="button" role="tab" aria-controls="panel-non-blud" aria-selected="false">Non BLUD <span class="badge badge-light-warning ms-2">{{ $rows->where('source', 'Non BLUD')->count() }}</span></button></li>
            </ul>
            <div class="tab-content">
                @foreach (['BLUD' => 'panel-blud', 'Non BLUD' => 'panel-non-blud'] as $source => $panelId)
                    <div class="tab-pane fade {{ $source === 'BLUD' ? 'show active' : '' }}" id="{{ $panelId }}" role="tabpanel" aria-labelledby="{{ $source === 'BLUD' ? 'tab-blud' : 'tab-non-blud' }}" tabindex="0">
                        <div class="table-responsive"><table class="table table-row-bordered table-row-gray-300 align-middle gy-3 js-table-subkategori">
                            <thead><tr class="text-muted fw-bold text-uppercase fs-7"><th style="width:50px">No.</th><th>Kategori / Subkategori RKBU</th><th class="text-end">Pagu subkategori</th><th class="text-end">Terpakai RKBU</th><th class="text-end">Sisa pagu</th><th class="text-end">Aksi</th></tr></thead>
                            <tbody>
                                @foreach ($rows->where('source', $source)->values() as $index => $row)
                                    @php($sisa = (float) ($row->pagu?->jumlah_pagu ?? 0) - (float) $row->used)
                                    <tr>
                                        <td class="text-muted">{{ $index + 1 }}</td>
                                        <td><div class="fw-semibold">{{ $row->sub->nama_sub_kategori_rkbu }}</div><div class="text-muted fs-7">{{ $row->sub->kategori?->nama_kategori_rkbu ?? 'Kategori tidak diatur' }} · {{ $row->sub->kode_sub_kategori_rkbu }}</div></td>
                                        <td class="text-end fw-semibold">{{ $row->pagu ? $rupiah($row->pagu->jumlah_pagu) : 'Belum diatur' }}</td>
                                        <td class="text-end">{{ $rupiah($row->used) }}</td>
                                        <td class="text-end {{ $row->pagu && $sisa < 0 ? 'text-danger fw-bold' : 'text-success' }}">{{ $row->pagu ? $rupiah($sisa) : '—' }}</td>
                                        <td class="text-end text-nowrap">
                                            @if ($row->pagu)
                                                <a href="{{ route('pagu_subkategori.edit', $row->pagu) }}" class="btn btn-sm btn-light-primary">Ubah</a>
                                                <form method="POST" action="{{ route('pagu_subkategori.destroy', $row->pagu) }}" class="d-inline" onsubmit="return confirm('Hapus pagu untuk subkategori dan sumber dana ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger">Hapus</button></form>
                                            @else
                                                <a href="{{ route('pagu_subkategori.create', ['tahun_anggaran_id' => $yearId, 'sub_kategori_rkbu_id' => $row->sub->id_sub_kategori_rkbu, 'jenis_sumber_dana' => $row->source]) }}" class="btn btn-sm btn-light-primary">Atur pagu</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>.pagu-summary-icon{width:48px;height:48px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;font-size:19px;flex:0 0 auto}</style>
    <script>
        document.getElementById('filter-subkategori')?.addEventListener('input', function () {
            const query = this.value.trim().toLocaleLowerCase();
            const activePanel = document.querySelector('.tab-pane.active');
            activePanel?.querySelectorAll('tbody tr').forEach(row => {
                row.hidden = !row.textContent.toLocaleLowerCase().includes(query);
            });
        });
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', () => document.getElementById('filter-subkategori').dispatchEvent(new Event('input')));
        });
    </script>
@endsection
