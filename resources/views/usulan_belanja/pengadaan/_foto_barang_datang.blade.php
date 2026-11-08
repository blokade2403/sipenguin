@php
    $fotoList = collect([
        $spj->foto_barang_datang,
        $spj->foto_barang_datang_2,
        $spj->foto_barang_datang_3,
    ])->filter()->merge(collect($arrivalReceipts ?? [])->flatMap(fn ($receipt) => [$receipt->foto_utama, $receipt->foto_tambahan_2, $receipt->foto_tambahan_3]))->filter()->unique()->values();
    $rkbuInfo = $spj->details->first()?->rkbu;
    $fotoLog = $logs->whereIn('aksi', ['Unggah foto barang datang', 'Catat penerimaan barang'])->last();
@endphp

<div class="card card-bordered mb-6 arrival-verification-card">
    <div class="card-header arrival-verification-header">
        <div class="d-flex align-items-center gap-3">
            <span class="arrival-section-icon"><i class="fas fa-box-open"></i></span>
            <div><span class="arrival-section-eyebrow">Penerimaan dan pemeriksaan</span><h4 class="card-title mb-0">Proses Verifikasi Barang Datang</h4></div>
        </div>
    </div>
    <div class="card-body">
        @if ($canFoto && $step === 'upload_foto' && $spj->status_verifikasi_pengurus_barang === 'Revisi')
            <div class="alert alert-warning arrival-revision-alert"><i class="fas fa-circle-exclamation me-2"></i><strong>Catatan Pengurus Barang:</strong>
                {{ $spj->keterangan_verif_pengurus_barang }}</div>
        @endif

        <div class="row g-4 g-xl-5 align-items-start arrival-layout">
            <div class="col-12 col-lg-5">
                <div class="arrival-gallery">
                    @if ($fotoList->isNotEmpty())
                        <div class="arrival-gallery-main">
                            <span class="arrival-gallery-count" id="arrival-gallery-main-count">1 / {{ $fotoList->count() }}</span>
                            <button type="button" class="arrival-gallery-nav is-previous" data-gallery-step="-1" aria-label="Foto sebelumnya">‹</button>
                            <img id="arrival-gallery-image" src="{{ asset('storage/' . $fotoList->first()) }}"
                                alt="Foto barang datang" data-gallery-index="0" data-bs-toggle="modal"
                                data-bs-target="#arrival-photo-modal">
                            <button type="button" class="arrival-gallery-nav is-next" data-gallery-step="1" aria-label="Foto berikutnya">›</button>
                        </div>
                        <div class="arrival-gallery-thumbnails">
                            @foreach ($fotoList as $index => $foto)
                                <button type="button" class="arrival-gallery-thumbnail {{ $index === 0 ? 'is-active' : '' }}"
                                    data-gallery-index="{{ $index }}" aria-label="Tampilkan foto {{ $index + 1 }}">
                                    <img src="{{ asset('storage/' . $foto) }}" alt="Foto barang datang {{ $index + 1 }}">
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="arrival-gallery-empty">
                            <i class="ki-duotone ki-picture fs-5x text-muted"><span class="path1"></span><span class="path2"></span></i>
                            <span>Foto barang datang belum diunggah</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-12 col-lg-7">
                <div class="arrival-overview">
                    @php $latestArrival = ($arrivalReceipts ?? collect())->last(); @endphp
                    <div class="arrival-overview-topline">
                        <div><span class="arrival-section-eyebrow">Nomor SPJ</span><h3 class="arrival-spj-number">{{ $spj->no_usulan_belanja }}-{{ str_pad((string) $spj->nomor_urut, 2, '0', STR_PAD_LEFT) }}</h3></div>
                        <span class="badge badge-light-primary fs-7 arrival-status-badge">{{ $spj->status_verifikasi_pengurus_barang ?: ($latestArrival ? ($latestArrival->status_penerimaan === 'selesai' ? 'Barang datang selesai' : 'Barang datang parsial') : 'Menunggu unggah foto') }}</span>
                    </div>
                    <div class="arrival-vendor"><i class="fas fa-building"></i><span>{{ $masterInfo?->nama_perusahaan ?? 'Penyedia belum ditentukan' }}</span></div>
                    <div class="arrival-meta"><span><i class="fas fa-box"></i> ID Paket <strong>{{ $spj->idpaket ?: ($masterInfo?->idpaket ?? '-') }}</strong></span><span><i class="fas fa-camera"></i> Update Foto <strong>{{ $fotoLog?->created_at ? \Illuminate\Support\Carbon::parse($fotoLog->created_at)->translatedFormat('d F Y') : '-' }}</strong></span></div>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-md-4">
                        <div class="arrival-info-box"><span>Total Anggaran</span><strong>Rp {{ number_format($totalAnggaran, 0, ',', '.') }}</strong></div>
                    </div>
                    <div class="col-md-4">
                        <div class="arrival-info-box"><span>Tgl. Surat Pesanan</span><strong>{{ $spj->tgl_surat_pesanan ? \Illuminate\Support\Carbon::parse($spj->tgl_surat_pesanan)->translatedFormat('d F Y') : '-' }}</strong></div>
                    </div>
                    <div class="col-md-4">
                        <div class="arrival-info-box"><span>No. Surat Pesanan</span><strong>{{ $spj->no_surat_pesanan ?: '-' }}</strong></div>
                    </div>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-md-6">
                        <article class="arrival-detail-card">
                        <h5 class="mb-2">Catatan Barang Datang</h5>
                        <p class="text-muted mb-0">{{ $spj->keterangan_barang_datang ?: 'Tidak ada catatan.' }}</p>
                        <div class="text-muted mt-2">Tanggal datang: {{ $spj->tgl_barang_datang ? \Illuminate\Support\Carbon::parse($spj->tgl_barang_datang)->translatedFormat('d F Y') : '-' }}</div>
                        </article>
                    </div>
                    <div class="col-md-6">
                        <article class="arrival-detail-card">
                        <h5 class="mb-2">Validasi Pengurus Barang</h5>
                        <ul class="text-muted ps-5 mb-0">
                            <li>Tanggal verifikasi: {{ $spj->tgl_verif_pengurus_barang ? \Illuminate\Support\Carbon::parse($spj->tgl_verif_pengurus_barang)->translatedFormat('d F Y') : '-' }}</li>
                            <li>Status: <span class="badge badge-light-info">{{ $spj->status_verifikasi_pengurus_barang ?: 'Menunggu' }}</span></li>
                            @if ($spj->keterangan_verif_pengurus_barang)
                                <li>Catatan: {{ $spj->keterangan_verif_pengurus_barang }}</li>
                            @endif
                        </ul>
                        </article>
                    </div>
                </div>

                @if (($arrivalReceipts ?? collect())->isNotEmpty())
                    <div class="card card-bordered mb-5 arrival-history-card">
                        <div class="card-header min-h-50px"><h5 class="card-title mb-0">Riwayat Barang Datang</h5></div>
                        <div class="card-body"><ul class="mb-0">@foreach ($arrivalReceipts as $receipt)<li class="mb-2"><strong>{{ \Illuminate\Support\Carbon::parse($receipt->tanggal_datang)->translatedFormat('d F Y') }}</strong> — {{ $receipt->status_penerimaan === 'selesai' ? 'Selesai' : 'Parsial' }}. {{ $receipt->keterangan ?: 'Tanpa keterangan.' }}</li>@endforeach</ul></div>
                    </div>
                @endif

                @if (filled($spj->rincian_belanja))
                    <div class="arrival-detail-card mb-5">
                        <h5 class="mb-2">Rincian Belanja</h5>
                        <div class="text-gray-700" style="white-space: pre-line">{{ $spj->rincian_belanja }}</div>
                    </div>
                @endif

                @if ($canFoto && $step === 'upload_foto')
                    <div class="card card-bordered spj-stage-form-card mb-5">
                        <div class="card-header min-h-50px"><h5 class="card-title">{{ $fotoList->isNotEmpty() ? 'Perbarui Barang Datang' : 'Catat Barang Datang' }}</h5></div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data" action="{{ route('usulan-belanja.pengadaan.foto', $spj->id) }}">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-4"><label class="form-label">Status kedatangan</label><select name="status_penerimaan" class="form-select" required><option value="parsial" @selected(old('status_penerimaan', 'parsial') === 'parsial')>Parsial — barang belum lengkap</option><option value="selesai" @selected(old('status_penerimaan') === 'selesai')>Selesai — semua barang sudah datang</option></select><div class="form-text">Pilih Selesai hanya jika seluruh barang pesanan sudah diterima.</div></div>
                                    <div class="col-md-4"><label class="form-label">Foto utama (wajib)</label><input type="file" name="foto_barang_datang" class="form-control" accept="image/jpeg,image/png" required></div>
                                    <div class="col-md-4"><label class="form-label">Foto tambahan 2</label><input type="file" name="foto_barang_datang_2" class="form-control" accept="image/jpeg,image/png"></div>
                                    <div class="col-md-4"><label class="form-label">Foto tambahan 3</label><input type="file" name="foto_barang_datang_3" class="form-control" accept="image/jpeg,image/png"></div>
                                    <div class="col-md-4"><label class="form-label">Tanggal barang datang</label><input type="date" name="tgl_barang_datang" class="form-control" value="{{ old('tgl_barang_datang', $spj->tgl_barang_datang) }}" required></div>
                                    <div class="col-md-8"><label class="form-label">Keterangan</label><textarea name="keterangan_barang_datang" class="form-control" rows="2">{{ old('keterangan_barang_datang', $spj->keterangan_barang_datang) }}</textarea></div>
                                </div>
                                <button class="btn btn-primary mt-4">Simpan barang datang</button>
                            </form>
                        </div>
                    </div>
                @endif

                <div class="arrival-information mt-6">
                    <h5 class="mb-3">Informasi Belanja</h5>
                    <ul class="nav nav-tabs nav-line-tabs mb-0" role="tablist">
                        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#arrival-account-tab" type="button">Detail Kode Rekening</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#arrival-vendor-tab" type="button">Data Penyedia</button></li>
                        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#arrival-bast-tab" type="button">Data BAST</button></li>
                    </ul>
                    <div class="tab-content border border-top-0 rounded-bottom p-4">
                        <div class="tab-pane fade show active" id="arrival-account-tab">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 py-2 border-bottom">Rekening Belanja</dt><dd class="col-sm-8 py-2 border-bottom">{{ $rkbuInfo?->rekeningBelanja?->kode_rekening_belanja ?? $rkbuInfo?->subKategoriRkbu?->sub_kategori_rekening?->kode_sub_kategori_rekening }} {{ $rkbuInfo?->rekeningBelanja?->nama_rekening_belanja ?? ($rkbuInfo?->subKategoriRkbu?->sub_kategori_rekening?->nama_sub_kategori_rekening ?? '-') }}</dd>
                                <dt class="col-sm-4 py-2 border-bottom">Jenis Kategori</dt><dd class="col-sm-8 py-2 border-bottom">{{ $rkbuInfo?->jenisKategoriRkbu?->nama_jenis_kategori_rkbu ?? '-' }}</dd>
                                <dt class="col-sm-4 py-2 border-bottom">Kategori</dt><dd class="col-sm-8 py-2 border-bottom">{{ $rkbuInfo?->subKategoriRkbu?->kategori?->nama_kategori_rkbu ?? '-' }}</dd>
                                <dt class="col-sm-4 py-2 border-bottom">Subkategori</dt><dd class="col-sm-8 py-2 border-bottom">{{ $rkbuInfo?->subKategoriRkbu?->kode_sub_kategori_rkbu }} {{ $rkbuInfo?->subKategoriRkbu?->nama_sub_kategori_rkbu ?? '-' }}</dd>
                                <dt class="col-sm-4 py-2 border-bottom">Jenis Belanja</dt><dd class="col-sm-8 py-2 border-bottom">{{ $rkbuInfo?->jenisBelanja?->nama_jenis_belanja ?? '-' }}</dd>
                                <dt class="col-sm-4 py-2">Sumber Dana</dt><dd class="col-sm-8 py-2">{{ $rkbuInfo?->sumberDana?->nama_sumber_dana ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="tab-pane fade" id="arrival-vendor-tab">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 py-2 border-bottom">Nama Penyedia</dt><dd class="col-sm-8 py-2 border-bottom">{{ $masterInfo?->nama_perusahaan ?? '-' }}</dd>
                                <dt class="col-sm-4 py-2 border-bottom">ID Paket</dt><dd class="col-sm-8 py-2 border-bottom">{{ $spj->idpaket ?: ($masterInfo?->idpaket ?? '-') }}</dd>
                                <dt class="col-sm-4 py-2 border-bottom">Nomor SPK</dt><dd class="col-sm-8 py-2 border-bottom">{{ $masterInfo?->nomor_spk ?? '-' }}</dd>
                                <dt class="col-sm-4 py-2">Nomor Surat Pesanan</dt><dd class="col-sm-8 py-2">{{ $spj->no_surat_pesanan ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="tab-pane fade" id="arrival-bast-tab">
                            <dl class="row mb-0">
                                <dt class="col-sm-4 py-2 border-bottom">Tanggal BAST</dt><dd class="col-sm-8 py-2 border-bottom">{{ $spj->tgl_bast ? \Illuminate\Support\Carbon::parse($spj->tgl_bast)->translatedFormat('d F Y') : '-' }}</dd>
                                <dt class="col-sm-4 py-2 border-bottom">Nomor BA</dt><dd class="col-sm-8 py-2 border-bottom">{{ $spj->no_ba ?? '-' }}</dd>
                                <dt class="col-sm-4 py-2 border-bottom">Nomor BA HP</dt><dd class="col-sm-8 py-2 border-bottom">{{ $spj->no_ba_hp ?? '-' }}</dd>
                                <dt class="col-sm-4 py-2 border-bottom">Nomor BA BP</dt><dd class="col-sm-8 py-2 border-bottom">{{ $spj->no_ba_bp ?? '-' }}</dd>
                                <dt class="col-sm-4 py-2">Nomor DPA</dt><dd class="col-sm-8 py-2">{{ $spj->no_dpa ?? '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($fotoList->isNotEmpty())
    <div class="modal fade" id="arrival-photo-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark">
                <div class="modal-header border-0"><button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Tutup"></button></div>
                <div class="modal-body d-flex align-items-center justify-content-center position-relative">
                    <button type="button" class="arrival-modal-nav is-previous" data-gallery-step="-1" aria-label="Foto sebelumnya">‹</button>
                    <img id="arrival-gallery-modal-image" src="{{ asset('storage/' . $fotoList->first()) }}" alt="Foto barang datang ukuran penuh">
                    <button type="button" class="arrival-modal-nav is-next" data-gallery-step="1" aria-label="Foto berikutnya">›</button>
                </div>
                <div class="text-center text-white pb-4"><span id="arrival-gallery-count">1 / {{ $fotoList->count() }}</span></div>
            </div>
        </div>
    </div>
@endif

<style>
    .arrival-verification-card { overflow: hidden; border: 1px solid #e7edf5; border-radius: 18px; box-shadow: 0 12px 34px rgba(31, 51, 82, .06); }
    .arrival-verification-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #edf1f7; background: linear-gradient(110deg, #f7faff, #fff 72%); }
    .arrival-section-icon { display: inline-grid; width: 44px; height: 44px; flex: 0 0 44px; place-items: center; border-radius: 14px; color: #168bea; background: #eaf5ff; font-size: 18px; }
    .arrival-section-eyebrow { display: block; margin-bottom: 3px; color: #929db0; font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
    .arrival-layout { align-items: start; }
    .arrival-gallery-main { position: relative; overflow: hidden; width: 100%; aspect-ratio: 4 / 3; padding: 8px; border: 1px solid #e8edf5; border-radius: 16px; background: #f4f7fb; box-shadow: 0 8px 24px rgba(31, 51, 82, .08); }
    .arrival-gallery-main img { display: block; width: 100%; height: 100%; object-fit: contain; border-radius: 10px; background: #fff; cursor: zoom-in; }
    .arrival-gallery-count { position: absolute; z-index: 2; top: 16px; right: 16px; padding: 5px 10px; border: 1px solid #ffffffa8; border-radius: 999px; color: #34445c; background: #ffffffd9; font-size: 12px; font-weight: 700; backdrop-filter: blur(8px); }
    .arrival-gallery-empty { aspect-ratio: 4 / 3; display: flex; flex-direction: column; gap: 12px; align-items: center; justify-content: center; border: 1px dashed #d7e0ec; border-radius: 16px; color: #7e8299; background: #f7f9fc; }
    .arrival-gallery-thumbnails { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
    .arrival-gallery-thumbnail { padding: 4px; border: 1px solid #e4eaf2; border-radius: 10px; background: #fff; cursor: pointer; transition: border-color .18s, box-shadow .18s, transform .18s; }
    .arrival-gallery-thumbnail:hover { transform: translateY(-2px); }
    .arrival-gallery-thumbnail.is-active { border: 2px solid #36c98f; box-shadow: 0 0 0 3px #36c98f20; }
    .arrival-gallery-thumbnail img { display: block; width: 72px; height: 82px; object-fit: cover; border-radius: 6px; }
    .arrival-gallery-nav, .arrival-modal-nav { position: absolute; z-index: 2; top: 50%; display: grid; width: 42px; height: 42px; place-items: center; border: 1px solid #ffffffb5; border-radius: 50%; color: #176fc0; background: #fff; font-size: 32px; line-height: 1; transform: translateY(-50%); box-shadow: 0 4px 16px #18243b24; transition: transform .18s, background .18s; }
    .arrival-gallery-nav:hover, .arrival-modal-nav:hover { background: #f0f7ff; transform: translateY(-50%) scale(1.05); }
    .arrival-gallery-nav.is-previous { left: 18px; } .arrival-gallery-nav.is-next { right: 18px; }
    .arrival-overview { padding-bottom: 1rem; border-bottom: 1px solid #edf1f6; }
    .arrival-overview-topline { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; }
    .arrival-spj-number { margin: 0; color: #172b4d; font-size: clamp(1.25rem, 2vw, 1.65rem); font-weight: 750; letter-spacing: -.025em; overflow-wrap: anywhere; }
    .arrival-status-badge { padding: .55rem .8rem; border-radius: 999px; white-space: normal; text-align: center; }
    .arrival-vendor { display: flex; align-items: center; gap: 8px; margin-top: 10px; color: #1888df; font-weight: 600; }
    .arrival-vendor i, .arrival-meta i { color: #93a4ba; }
    .arrival-meta { display: flex; flex-wrap: wrap; gap: 8px 18px; margin-top: 12px; color: #8995a8; font-size: 13px; }
    .arrival-meta span { display: inline-flex; align-items: center; gap: 7px; }
    .arrival-meta strong { color: #46566d; font-weight: 600; overflow-wrap: anywhere; }
    .arrival-info-box { height: 100%; min-height: 86px; padding: 15px 16px; border: 1px solid #e8eef6; border-radius: 13px; display: flex; flex-direction: column; justify-content: center; gap: 5px; background: linear-gradient(145deg, #f8fbff, #fff 80%); }
    .arrival-info-box span { color: #929db0; font-size: 12px; font-weight: 600; } .arrival-info-box strong { color: #34445c; font-size: 14px; overflow-wrap: anywhere; }
    .arrival-detail-card { height: 100%; padding: 16px 18px; border: 1px solid #e8eef5; border-radius: 14px; background: #fff; }
    .arrival-detail-card h5 { color: #24364d; font-size: 14px; font-weight: 700; }
    .arrival-detail-card p, .arrival-detail-card li, .arrival-detail-card > div { font-size: 13px; line-height: 1.65; }
    .arrival-history-card { padding: 17px 18px; border: 1px solid #e8eef5; border-radius: 14px; background: #f9fbfe; }
    .arrival-history-card h5 { margin: 0 0 14px; color: #24364d; font-size: 14px; font-weight: 700; }
    .arrival-history-list { display: grid; gap: 12px; }
    .arrival-history-item { display: flex; gap: 11px; color: #34445c; font-size: 13px; }
    .arrival-history-dot { width: 9px; height: 9px; flex: 0 0 9px; margin-top: 5px; border-radius: 50%; background: #21bd87; box-shadow: 0 0 0 4px #21bd871a; }
    .arrival-history-item .text-muted { margin-top: 2px; line-height: 1.55; }
    .arrival-information > h5 { color: #24364d; font-weight: 700; }
    .arrival-information .tab-content { border-color: #e8eef5 !important; border-radius: 0 0 14px 14px !important; }
    .arrival-information .nav-link { font-weight: 600; }
    .arrival-revision-alert { border: 0; border-radius: 12px; }
    .spj-stage-form-card { overflow: hidden; border: 1px solid #e6edf5; border-radius: 14px; box-shadow: 0 6px 18px #1b35520a; }
    #arrival-gallery-modal-image { max-width: 92vw; max-height: 82vh; object-fit: contain; }
    .arrival-modal-nav { position: fixed; top: 50%; width: 52px; height: 52px; font-size: 40px; }
    .arrival-modal-nav.is-previous { left: 24px; } .arrival-modal-nav.is-next { right: 24px; }
    @media (max-width: 991.98px) { .arrival-gallery-main, .arrival-gallery-empty { aspect-ratio: 16 / 10; } }
    @media (max-width: 575.98px) {
        .arrival-verification-header { padding: 1rem; }
        .arrival-verification-card > .card-body { padding: 1rem; }
        .arrival-overview-topline { flex-direction: column; align-items: flex-start; }
        .arrival-info-box { min-height: 74px; padding: 12px; }
        .arrival-gallery-main, .arrival-gallery-empty { aspect-ratio: 1 / 1; }
        .arrival-gallery-thumbnail img { width: 62px; height: 70px; }
        .arrival-meta { display: grid; }
        .arrival-modal-nav { width: 42px; height: 42px; font-size: 32px; }
        .arrival-modal-nav.is-previous { left: 10px; } .arrival-modal-nav.is-next { right: 10px; }
    }
</style>

@if ($fotoList->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const photos = @json($fotoList->map(fn ($path) => asset('storage/' . $path))->all());
            const mainImage = document.getElementById('arrival-gallery-image');
            const modalImage = document.getElementById('arrival-gallery-modal-image');
            const count = document.getElementById('arrival-gallery-count');
            const mainCount = document.getElementById('arrival-gallery-main-count');
            const thumbnails = Array.from(document.querySelectorAll('.arrival-gallery-thumbnail'));
            let activeIndex = 0;

            function showPhoto(index) {
                activeIndex = (index + photos.length) % photos.length;
                if (mainImage) {
                    mainImage.src = photos[activeIndex];
                    mainImage.dataset.galleryIndex = activeIndex;
                }
                if (modalImage) modalImage.src = photos[activeIndex];
                if (count) count.textContent = `${activeIndex + 1} / ${photos.length}`;
                if (mainCount) mainCount.textContent = `${activeIndex + 1} / ${photos.length}`;
                thumbnails.forEach((thumbnail, thumbnailIndex) => thumbnail.classList.toggle('is-active', thumbnailIndex === activeIndex));
            }

            thumbnails.forEach((thumbnail) => thumbnail.addEventListener('click', () => showPhoto(Number(thumbnail.dataset.galleryIndex))));
            document.querySelectorAll('[data-gallery-step]').forEach((button) => button.addEventListener('click', () => showPhoto(activeIndex + Number(button.dataset.galleryStep))));
        });
    </script>
@endif
