@extends('layouts.main')

@section('content')
    @php($isExpense = in_array($item->section, ['belanja_operasi', 'belanja_modal'], true))
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-6">
        <div><h1 class="fs-2 fw-bold text-gray-900 mb-1">Atur Pemetaan Ikhtisar</h1><div class="text-muted">{{ $item->kode_ikhtisar }} · {{ $item->nama_ikhtisar }}</div></div>
        <a href="{{ route('rba-ikhtisar.mappings') }}" class="btn btn-light">Kembali</a>
    </div>
    @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if ($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="card border-0 shadow-sm mb-6"><div class="card-header"><h2 class="card-title fs-4">Rekening sumber nilai anggaran</h2></div><div class="card-body">
        <form method="POST" action="{{ route('rba-ikhtisar.mapping.update', $item) }}">
            @csrf @method('PUT')
            @if ($isExpense)
                <label class="form-label fw-semibold">Subkategori RKBU</label>
                <select name="sub_kategori_rkbu_ids[]" class="form-select" data-control="select2" data-placeholder="Pilih satu atau beberapa subkategori" multiple>
                    @foreach ($subcategories as $sub)
                        <option value="{{ $sub->id_sub_kategori_rkbu }}" @selected($item->mappings->contains('sub_kategori_rkbu_id', $sub->id_sub_kategori_rkbu))>{{ $sub->kode_sub_kategori_rkbu }} · {{ $sub->nama_sub_kategori_rkbu }}</option>
                    @endforeach
                </select>
                <div class="form-text mt-2">Pagu BLUD dan Non BLUD dari subkategori terpilih akan masuk ke baris ini. Jika baris ini memiliki subbaris, petakan pada baris ini atau subbarisnya saja agar pagu tidak dihitung ganda.</div>
            @else
                <div class="row g-4">
                    <div class="col-md-4"><label class="form-label fw-semibold">Sumber dana laporan</label><select class="form-select" name="jenis_sumber_dana" required><option value="BLUD" @selected($item->mappings->first()?->jenis_sumber_dana === 'BLUD')>BLUD</option><option value="Non BLUD" @selected($item->mappings->first()?->jenis_sumber_dana === 'Non BLUD')>APBD (Non BLUD)</option></select></div>
                    <div class="col-md-8"><label class="form-label fw-semibold">Rekening pendapatan</label><select name="rekening_pendapatan_ids[]" class="form-select" data-control="select2" data-placeholder="Pilih rekening pendapatan" multiple>@foreach ($incomeAccounts as $account)<option value="{{ $account->id }}" @selected($item->mappings->contains('rekening_pendapatan_id', $account->id))>{{ $account->kode_rekening }} · {{ $account->nama_rekening }}</option>@endforeach</select></div>
                </div>
                <div class="form-text mt-2">Untuk pembiayaan, pilih rekening pendapatan/SILPA yang sesuai. Label Non BLUD akan ditampilkan sebagai APBD.</div>
            @endif
            <div class="text-end mt-5"><button class="btn btn-primary">Simpan Pemetaan</button></div>
        </form>
    </div></div>

    <div class="card border-0 shadow-sm"><div class="card-header"><h2 class="card-title fs-4">Realisasi historis</h2></div><div class="card-body">
        <p class="text-muted">Isi nilai realisasi pembanding untuk baris ini. Angka disimpan terpisah dan tidak mengubah transaksi atau nilai target yang sudah ada.</p>
        <form method="POST" action="{{ route('rba-ikhtisar.realisasi.save', $item) }}" class="row g-4 align-items-end">
            @csrf @method('PUT')
            <div class="col-md-3"><label class="form-label fw-semibold">Tahun realisasi</label><select name="tahun_anggaran_id" class="form-select" required>@foreach ($years as $year)<option value="{{ $year->id }}" @selected($year->id === $historyYearId)>{{ $year->nama_tahun_anggaran }}</option>@endforeach</select></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Realisasi BLUD</label><input type="number" name="realisasi_blud" class="form-control" min="0" step="0.01" value="{{ old('realisasi_blud', $historyValues->get('BLUD')?->jumlah_realisasi ?? 0) }}" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Realisasi APBD (Non BLUD)</label><input type="number" name="realisasi_non_blud" class="form-control" min="0" step="0.01" value="{{ old('realisasi_non_blud', $historyValues->get('Non BLUD')?->jumlah_realisasi ?? 0) }}" required></div>
            <div class="col-md-3"><label class="form-label fw-semibold">Sumber data</label><input type="text" name="keterangan_sumber" class="form-control" value="{{ old('keterangan_sumber', $historyValues->first()?->keterangan_sumber) }}" placeholder="Mis. Ikhtisar RBA 2025"></div>
            <div class="col-12 text-end"><button class="btn btn-success">Simpan Realisasi</button></div>
        </form>
    </div></div>
@endsection
