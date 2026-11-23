@extends('layouts.main')

@section('content')
    <style>
        .company-hero { position: relative; overflow: hidden; border: 0; border-radius: 1rem; background: linear-gradient(115deg, #f1f7ff 0%, #fff 72%); }
        .company-hero::after { content: ''; position: absolute; width: 190px; height: 190px; right: -55px; top: -95px; border-radius: 50%; background: rgba(54, 153, 255, .08); pointer-events: none; }
        .company-hero-body { position: relative; z-index: 1; }
        .company-icon { display: inline-flex; width: 52px; height: 52px; flex: 0 0 52px; align-items: center; justify-content: center; border-radius: .9rem; background: #e8f3ff; color: #3699ff; font-size: 1.35rem; }
        .company-modal-icon { display: inline-flex; width: 44px; height: 44px; align-items: center; justify-content: center; border-radius: 12px; color: #3699ff; background: #eef6ff; }
        .company-table td { vertical-align: middle; }
        .company-table-controls { display:flex; justify-content:space-between; align-items:center; gap:16px; margin-bottom:14px; color:#181c32; }
        .company-page-size-control, .company-search-control { display:flex; align-items:center; gap:8px; }
        .company-page-size-control select { width:76px; }
        .company-search-control input { width:210px; }
        @media (max-width: 575.98px) { .company-hero-body { align-items: flex-start !important; flex-direction: column; padding: 1.15rem !important; } .company-add-action { width: 100%; } }
        @media (max-width: 575.98px) { .company-table-controls { align-items:stretch; flex-direction:column; } .company-search-control { justify-content:space-between; } }
    </style>

    <div class="card company-hero mb-6 shadow-sm">
        <div class="card-body company-hero-body d-flex flex-wrap align-items-center justify-content-between gap-4 p-5 p-lg-6">
            <div class="d-flex align-items-center gap-4">
                <span class="company-icon" aria-hidden="true"><i class="fas fa-building"></i></span>
                <div>
                    <h1 class="fs-2 fw-bold text-gray-900 mb-1">Data Perusahaan</h1>
                    <div class="text-muted">Kelola informasi perusahaan rekanan pengadaan.</div>
                </div>
            </div>
            <button type="button" class="btn btn-primary company-add-action" data-bs-toggle="modal" data-bs-target="#companyCreateModal">
                <i class="fas fa-plus me-2"></i>Tambah Perusahaan
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success d-flex align-items-center"><i class="fas fa-check-circle me-3"></i>{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger d-flex align-items-center"><i class="fas fa-exclamation-circle me-3"></i>{{ session('error') }}</div>
    @endif

    <div class="row gy-5 g-xl-8">
        <div class="col-12">
            <div class="card card-xl-stretch shadow-sm">
                <div class="card-header border-0 pt-5">
                    <div class="card-title d-flex flex-column">
                        <h2 class="fw-bold text-gray-900 mb-1">Daftar Perusahaan</h2>
                        <span class="text-muted fs-7">{{ $perusahaans->total() }} perusahaan terdaftar</span>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <div class="company-table-controls">
                        <form method="GET" action="{{ route('perusahaans.index') }}" class="company-page-size-control">
                            @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                            <label for="company-per-page">Tampilkan</label>
                            <select id="company-per-page" name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach([5, 10, 15, 25, 50, 100] as $option)
                                    <option value="{{ $option }}" @selected((int) request('per_page', 15) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                            <span>data per halaman</span>
                        </form>
                        <form method="GET" action="{{ route('perusahaans.index') }}" class="company-search-control">
                            @if(request('per_page')) <input type="hidden" name="per_page" value="{{ request('per_page') }}"> @endif
                            <label for="company-search">Cari:</label>
                            <input type="search" id="company-search" name="search" class="form-control form-control-sm" value="{{ request('search') }}" aria-label="Cari data perusahaan">
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table id="responsive-reorder" class="table company-table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="min-w-50px">No.</th>
                                    <th class="min-w-250px">Perusahaan</th>
                                    <th class="min-w-220px">Kontak</th>
                                    <th class="min-w-250px">Alamat</th>
                                    <th class="min-w-140px">Terdaftar</th>
                                    <th class="min-w-100px text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($perusahaans as $index => $item)
                                    <tr>
                                        <td class="text-muted">{{ $perusahaans->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-3"><span class="symbol-label bg-light-primary text-primary fw-bold"><i class="fas fa-building"></i></span></div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-gray-900 fw-bold">{{ $item->nama_perusahaan }}</span>
                                                    <span class="text-muted fs-7">NPWP: {{ $item->no_npwp ?: '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="mailto:{{ $item->email_perusahaan }}" class="text-gray-800 text-hover-primary fw-semibold"><i class="fas fa-envelope text-muted me-2"></i>{{ $item->email_perusahaan }}</a>
                                                <a href="tel:{{ $item->tlp_perusahaan }}" class="text-muted fs-7 mt-1"><i class="fas fa-phone text-muted me-2"></i>{{ $item->tlp_perusahaan ?: '-' }}</a>
                                                <span class="text-muted fs-8 mt-1">{{ $item->nama_direktur_perusahaan }} · {{ $item->jabatan_perusahaan }}</span>
                                            </div>
                                        </td>
                                        <td class="text-gray-700">{{ $item->alamat_perusahaan ?: '-' }}</td>
                                        <td class="text-muted">{{ optional($item->created_at)->format('d M Y') ?: '-' }}</td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-bs-toggle="modal" data-bs-target="#companyEditModal{{ $item->id_perusahaan }}" aria-label="Edit {{ $item->nama_perusahaan }}" title="Edit"><i class="fas fa-pen"></i></button>
                                                <form action="{{ route('perusahaans.destroy', $item->id_perusahaan) }}" method="POST" class="form-delete m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm" aria-label="Hapus {{ $item->nama_perusahaan }}" title="Hapus"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="text-center text-muted py-10"><i class="fas fa-building fs-2x d-block mb-3"></i>Belum ada perusahaan. Tambahkan data perusahaan untuk mulai.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mt-4">
                        <span class="text-muted fs-7">Menampilkan {{ $perusahaans->firstItem() ?? 0 }} sampai {{ $perusahaans->lastItem() ?? 0 }} dari {{ $perusahaans->total() }} data</span>
                        {{ $perusahaans->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('pengadaan.perusahaan.modal_add')
    @include('pengadaan.perusahaan.modal_edit')
@endsection

@push('scripts')
    <script>
        (() => {
            const search = document.getElementById('company-search');
            if (!search) return;
            let timer;
            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => search.form.requestSubmit(), 350);
            });
        })();
    </script>
@endpush
