@extends('layouts.main')

@section('content')
    @php
        $rupiah = fn ($value) => number_format((float) $value, 0, ',', '.');
        $sectionLabels = [
            'pendapatan' => 'PENDAPATAN',
            'belanja_operasi' => 'BELANJA OPERASI',
            'belanja_modal' => 'BELANJA MODAL',
            'ringkasan' => 'RINGKASAN',
            'penerimaan_pembiayaan' => 'PEMBIAYAAN BLUD · PENERIMAAN',
            'pengeluaran_pembiayaan' => 'PEMBIAYAAN BLUD · PENGELUARAN',
        ];
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-6 rba-toolbar">
        <div>
            <h1 class="fs-2 fw-bold text-gray-900 mb-1">Ikhtisar Rencana Bisnis Anggaran (RBA) BLUD</h1>
            <div class="text-muted">{{ $year?->nama_tahun_anggaran ?? 'Tahun anggaran belum dipilih' }}{{ $year?->fase?->nama_fase ? ' · Fase '.$year->fase->nama_fase : '' }}</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if (auth()->user()?->hasRole('Administrator') || auth()->user()?->hasRole('Super Admin'))
                <a class="btn btn-light-primary" href="{{ route('rba-ikhtisar.mappings') }}"><i class="fas fa-diagram-project me-2"></i>Pemetaan Ikhtisar</a>
            @endif
            <button type="button" class="btn btn-success" onclick="window.print()"><i class="fas fa-file-pdf me-2"></i>Cetak / Simpan PDF</button>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-5 rba-toolbar">
        <div class="card-body">
            <form method="GET" action="{{ route('rba-ikhtisar.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="tahun_anggaran_id" class="form-label fw-semibold">Tahun anggaran</label>
                    <select id="tahun_anggaran_id" name="tahun_anggaran_id" class="form-select" data-control="select2" data-placeholder="Pilih tahun">
                        @foreach ($years as $option)
                            <option value="{{ $option->id }}" @selected($option->id === $yearId)>{{ $option->nama_tahun_anggaran }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="realisasi_tahun_anggaran_id" class="form-label fw-semibold">Tahun realisasi pembanding</label>
                    <select id="realisasi_tahun_anggaran_id" name="realisasi_tahun_anggaran_id" class="form-select" data-control="select2" data-placeholder="Pilih tahun realisasi">
                        <option value="">Tidak dipilih</option>
                        @foreach ($years as $option)
                            <option value="{{ $option->id }}" @selected($option->id === $realizationYearId)>{{ $option->nama_tahun_anggaran }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto"><button type="submit" class="btn btn-primary">Tampilkan</button></div>
            </form>
        </div>
    </div>

    @if ($unmappedCount > 0 && (auth()->user()?->hasRole('Administrator') || auth()->user()?->hasRole('Super Admin')))
        <div class="alert alert-warning d-flex align-items-center justify-content-between gap-3 rba-toolbar">
            <span>{{ $unmappedCount }} baris rincian belum dipetakan. Nilainya belum masuk ke Ikhtisar.</span>
            <a href="{{ route('rba-ikhtisar.mappings') }}" class="btn btn-sm btn-warning">Lengkapi pemetaan</a>
        </div>
    @endif

    @if (! $year)
        <div class="alert alert-info">Data tahun anggaran belum tersedia.</div>
    @else
        <div class="card border-0 shadow-sm rba-card">
            <div class="card-body p-4 p-lg-6">
                <div class="text-center mb-6 rba-heading">
                    <h2 class="fs-3 fw-bold mb-1">IKHTISAR RENCANA BISNIS ANGGARAN (RBA) BLUD</h2>
                    <div class="fw-semibold">TAHUN ANGGARAN {{ $year->nama_tahun_anggaran }}</div>
                    @if ($year->fase?->nama_fase)<div>FASE: {{ $year->fase->nama_fase }}</div>@endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle rba-table">
                        <thead>
                            <tr><th rowspan="2" class="text-center" style="width:65px">No</th><th rowspan="2">Uraian</th><th colspan="3" class="text-center">{{ $year->nama_tahun_anggaran }}</th><th rowspan="2" class="text-end">Realisasi {{ $realizationYear?->nama_tahun_anggaran ?? '—' }}</th><th rowspan="2" class="text-end">Selisih</th><th rowspan="2" class="text-end">Persentase</th></tr>
                            <tr><th class="text-end">BLUD</th><th class="text-end">APBD</th><th class="text-end">Jumlah</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($reportRows->groupBy(fn ($row) => $row['item']->section) as $section => $sectionRows)
                                <tr class="rba-section-row"><th colspan="8">{{ $sectionLabels[$section] ?? strtoupper(str_replace('_', ' ', $section)) }}</th></tr>
                                @foreach ($sectionRows as $row)
                                    @if ($row['item']->jenis_baris === 'header') @continue @endif
                                    @php($item = $row['item'])
                                    <tr class="{{ $item->jenis_baris === 'total' ? 'rba-total-row' : '' }}">
                                        <td class="text-center text-nowrap">{{ $item->kode_ikhtisar }}</td>
                                        <td><div class="rba-label" style="padding-left: {{ $item->level * 18 }}px">{{ $item->nama_ikhtisar }}</div></td>
                                        <td class="text-end text-nowrap">{{ $rupiah($row['values']['BLUD']) }}</td>
                                        <td class="text-end text-nowrap">{{ $rupiah($row['values']['Non BLUD']) }}</td>
                                        <td class="text-end text-nowrap">{{ $rupiah($row['jumlah']) }}</td>
                                        <td class="text-end text-nowrap">{{ $rupiah($row['realisasi']) }}</td>
                                        <td class="text-end text-nowrap">{{ $rupiah($row['selisih']) }}</td>
                                        <td class="text-end text-nowrap">{{ $row['persentase'] === null ? '—' : number_format($row['persentase'], 2, ',', '.') . '%' }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-muted fs-8 mt-3 rba-note">APBD pada laporan ini menggunakan data sumber dana Non BLUD. Realisasi pembanding diisi melalui pemetaan nilai historis.</div>
            </div>
        </div>
    @endif

    <style>
        .rba-card { border-radius: 12px; }
        .rba-table { min-width: 1050px; margin-bottom: 0; color: #24364b; font-size: 12px; }
        .rba-table th, .rba-table td { padding: 7px 9px; border-color: #adb8c5 !important; }
        .rba-table thead th { background: #eef2f6; font-weight: 700; }
        .rba-section-row th { background: #e4ebf3; color: #263b53; letter-spacing: .025em; }
        .rba-total-row { font-weight: 700; background: #f7f9fb; }
        .rba-label { min-width: 300px; }
        .rba-heading { line-height: 1.6; }
        @media print {
            @page { size: A4 landscape; margin: 10mm; }
            body, #kt_content, #kt_post, #kt_content_container { background: #fff !important; margin: 0 !important; padding: 0 !important; }
            #kt_aside, #kt_header, #kt_toolbar, .rba-toolbar, .rba-note, .btn, .card-header { display: none !important; }
            .wrapper, .content, .post, #kt_wrapper { margin: 0 !important; padding: 0 !important; width: 100% !important; }
            .rba-card, .card, .card-body { border: 0 !important; box-shadow: none !important; padding: 0 !important; }
            .rba-table { min-width: 0; width: 100%; font-size: 8pt; }
            .rba-table th, .rba-table td { padding: 3px 4px; }
            .rba-section-row, .rba-total-row { break-inside: avoid; }
        }
    </style>
@endsection
