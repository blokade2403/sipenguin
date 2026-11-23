@extends('layouts.main')
@section('content')
@php($editing = $mode === 'update')
<div class="d-flex justify-content-between align-items-center mb-5"><div><h1 class="fs-2 fw-bold">{{ $editing ? 'Ubah Pagu Subkategori' : 'Tambah Pagu Subkategori' }}</h1><div class="text-muted">Pagu ditetapkan untuk kombinasi tahun, subkategori RKBU, dan sumber dana.</div></div><a href="{{ route('pagu_subkategori.index', ['tahun_anggaran_id' => $pagu->tahun_anggaran_id]) }}" class="btn btn-light">Kembali</a></div>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="card"><form method="POST" action="{{ $editing ? route('pagu_subkategori.update',$pagu) : route('pagu_subkategori.store') }}">
    @csrf @if($editing) @method('PUT') @endif
    <div class="card-body"><div class="row g-5">
        <div class="col-md-6"><label class="form-label required">Tahun anggaran</label><select name="tahun_anggaran_id" class="form-select" required><option value="">Pilih tahun</option>@foreach($years as $year)<option value="{{ $year->id }}" @selected(old('tahun_anggaran_id',$pagu->tahun_anggaran_id)===$year->id)>{{ $year->nama_tahun_anggaran }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label required">Sumber dana</label><select name="jenis_sumber_dana" class="form-select" required><option value="">Pilih sumber dana</option>@foreach(['BLUD','Non BLUD'] as $source)<option value="{{ $source }}" @selected(old('jenis_sumber_dana',$pagu->jenis_sumber_dana)===$source)>{{ $source }}</option>@endforeach</select></div>
        <div class="col-md-8"><label class="form-label required">Subkategori RKBU</label><select name="sub_kategori_rkbu_id" class="form-select" data-control="select2" data-placeholder="Pilih subkategori" required><option value="">Pilih subkategori</option>@foreach($subkategoris as $sub)<option value="{{ $sub->id_sub_kategori_rkbu }}" @selected(old('sub_kategori_rkbu_id',$pagu->sub_kategori_rkbu_id)===$sub->id_sub_kategori_rkbu)>{{ $sub->kode_sub_kategori_rkbu }} — {{ $sub->nama_sub_kategori_rkbu }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="form-label required">Nominal pagu</label><div class="input-group"><span class="input-group-text">Rp</span><input type="number" name="jumlah_pagu" class="form-control" min="0" step="0.01" value="{{ old('jumlah_pagu',$pagu->jumlah_pagu) }}" required></div></div>
    </div></div>
    <div class="card-footer d-flex justify-content-end gap-2"><a href="{{ route('pagu_subkategori.index', ['tahun_anggaran_id' => $pagu->tahun_anggaran_id]) }}" class="btn btn-light">Batal</a><button class="btn btn-primary">{{ $editing ? 'Simpan Perubahan' : 'Simpan Pagu' }}</button></div>
</form></div>
@endsection
