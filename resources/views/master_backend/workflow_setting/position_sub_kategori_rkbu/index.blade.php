@extends('layouts.main')

@section('content')
    @include('components.partials.table.headermenu', ['title' => 'Pemetaan Jabatan dan Subkategori RKBU'])
    @include('master_backend.workflow_setting.position_sub_kategori_rkbu.modal_add')

    <style>
        .position-map-group { border: 1px solid #e5eaf1; border-radius: 12px; overflow: hidden; background: #fff; }
        .position-map-group + .position-map-group { margin-top: 1rem; }
        .position-map-heading { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.25rem; background: #f6f9fc; border-bottom: 1px solid #e5eaf1; }
        .position-map-heading h3 { margin: 0; color: #1e293b; font-size: 1rem; font-weight: 700; }
        .position-map-entry { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .9rem 1.25rem; }
        .position-map-entry + .position-map-entry { border-top: 1px solid #eef1f5; }
        .position-map-name { color: #334155; font-weight: 600; }
        .position-map-code { color: #64748b; font-size: .8rem; }
    </style>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header border-0 pt-5">
            <div>
                <h2 class="card-title fs-4 fw-bold text-gray-900 mb-1">Pemetaan per jabatan</h2>
                <p class="text-muted mb-0">Subkategori RKBU dikelompokkan menurut jabatan yang bertanggung jawab.</p>
            </div>
        </div>
        <div class="card-body pt-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-5">
                <div class="text-muted">{{ $data->count() }} pemetaan · {{ $data->groupBy(fn ($item) => $item->position?->id ?? 'unknown')->count() }} jabatan</div>
                <div class="position-relative">
                    <i class="fas fa-search position-absolute top-50 translate-middle-y ms-4 text-muted" aria-hidden="true"></i>
                    <input type="search" id="positionMappingSearch" class="form-control ps-12 w-275px" placeholder="Cari jabatan atau subkategori..." aria-label="Cari jabatan atau subkategori">
                </div>
            </div>

            <div id="positionMappingGroups">
                @forelse ($data->groupBy(fn ($item) => $item->position?->id ?? 'unknown') as $mappings)
                    @php $position = $mappings->first()->position; @endphp
                    <section class="position-map-group" data-position-search="{{ $position?->nama_jabatan ?? 'Jabatan tidak ditemukan' }}">
                        <header class="position-map-heading">
                            <h3><i class="fas fa-briefcase me-2 text-primary" aria-hidden="true"></i>{{ $position?->nama_jabatan ?? 'Jabatan tidak ditemukan' }}</h3>
                            <span class="badge badge-light-primary">{{ $mappings->count() }} subkategori</span>
                        </header>
                        <div>
                            @foreach ($mappings as $item)
                                <div class="position-map-entry" data-entry-search="{{ $item->subKategoriRkbu?->kode_sub_kategori_rkbu }} {{ $item->subKategoriRkbu?->nama_sub_kategori_rkbu }}">
                                    <div>
                                        @if ($item->subKategoriRkbu)
                                            <div class="position-map-name">{{ $item->subKategoriRkbu->nama_sub_kategori_rkbu }}</div>
                                            <div class="position-map-code">Kode {{ $item->subKategoriRkbu->kode_sub_kategori_rkbu ?? '—' }}</div>
                                        @else
                                            <div class="text-muted">Subkategori tidak ditemukan</div>
                                        @endif
                                    </div>
                                    <form action="{{ route('position_sub_kategori_rkbu.destroy', $item->id) }}" method="POST" class="flex-shrink-0" onsubmit="return confirm('Hapus pemetaan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light-danger" aria-label="Hapus pemetaan"><i class="fas fa-trash me-1" aria-hidden="true"></i>Hapus</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="text-center text-muted py-10">Belum ada pemetaan jabatan dan subkategori RKBU.</div>
                @endforelse
            </div>
            <div id="positionMappingNoResults" class="text-center text-muted py-10 d-none">Tidak ada pemetaan yang cocok dengan pencarian.</div>
        </div>
    </div>

    <script>
        (() => {
            const search = document.getElementById('positionMappingSearch');
            const groups = [...document.querySelectorAll('.position-map-group')];
            const noResults = document.getElementById('positionMappingNoResults');
            search?.addEventListener('input', () => {
                const term = search.value.trim().toLocaleLowerCase();
                let visibleGroups = 0;
                groups.forEach(group => {
                    const positionMatches = group.dataset.positionSearch.toLocaleLowerCase().includes(term);
                    const entries = [...group.querySelectorAll('.position-map-entry')];
                    let visibleEntries = 0;
                    entries.forEach(entry => {
                        const visible = !term || positionMatches || entry.dataset.entrySearch.toLocaleLowerCase().includes(term);
                        entry.classList.toggle('d-none', !visible);
                        if (visible) visibleEntries++;
                    });
                    group.classList.toggle('d-none', visibleEntries === 0);
                    if (visibleEntries) visibleGroups++;
                });
                noResults.classList.toggle('d-none', visibleGroups > 0 || groups.length === 0);
            });
        })();
    </script>
@endsection
