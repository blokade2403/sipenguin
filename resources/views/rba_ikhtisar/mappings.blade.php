@extends('layouts.main')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-6">
        <div><h1 class="fs-2 fw-bold text-gray-900 mb-1">Pemetaan Ikhtisar RBA</h1><div class="text-muted">Hubungkan baris Ikhtisar dengan rekening database tanpa mengubah kode/nama subkategori asli.</div></div>
        <a href="{{ route('rba-ikhtisar.index') }}" class="btn btn-light-primary">Kembali ke Ikhtisar</a>
    </div>
    <div class="alert alert-info">Baris belanja dipetakan ke subkategori RKBU. Baris pendapatan dan pembiayaan dipetakan ke rekening pendapatan serta sumber dananya.</div>
    <div class="card border-0 shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table table-row-bordered align-middle gy-3">
        <thead><tr class="text-muted fw-bold text-uppercase fs-7"><th>No.</th><th>Bagian</th><th>Kode Ikhtisar</th><th>Nama Ikhtisar</th><th>Pemetaan</th><th></th></tr></thead>
        <tbody>
            @foreach ($items->where('jenis_baris', 'detail') as $item)
                <tr>
                    <td>{{ $item->urutan }}</td><td>{{ str_replace('_', ' ', ucfirst($item->section)) }}</td><td>{{ $item->kode_ikhtisar }}</td>
                    <td><div style="padding-left: {{ $item->level * 16 }}px">{{ $item->nama_ikhtisar }}</div></td>
                    <td>
                        @forelse ($item->mappings as $mapping)
                            <div class="small">{{ $mapping->subkategori ? $mapping->subkategori->kode_sub_kategori_rkbu.' · '.$mapping->subkategori->nama_sub_kategori_rkbu : 'Rekening pendapatan' }}{{ $mapping->jenis_sumber_dana ? ' · '.($mapping->jenis_sumber_dana === 'Non BLUD' ? 'APBD' : 'BLUD') : '' }}</div>
                        @empty <span class="text-muted">Belum dipetakan</span> @endforelse
                    </td>
                    <td class="text-end"><a href="{{ route('rba-ikhtisar.mapping.edit', $item) }}" class="btn btn-sm btn-light-primary">Atur</a></td>
                </tr>
            @endforeach
        </tbody>
    </table></div></div></div>
@endsection
