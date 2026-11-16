<div class="modal fade" id="companyCreateModal" tabindex="-1" aria-labelledby="companyCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-3">
            <div class="modal-header border-0 pb-0 px-6 px-lg-8 pt-6">
                <div class="d-flex align-items-center gap-3">
                    <span class="company-modal-icon"><i class="fas fa-building"></i></span>
                    <div><h2 class="modal-title fs-3 fw-bold text-gray-900" id="companyCreateModalLabel">Tambah Perusahaan</h2><div class="text-muted fs-7">Lengkapi informasi perusahaan rekanan.</div></div>
                </div>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal" aria-label="Tutup"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('perusahaans.store') }}" method="POST">
                @csrf
                <div class="modal-body px-6 px-lg-8 py-6">
                    <div class="row g-5">
                        <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-create-name">Nama Perusahaan</label><input id="company-create-name" type="text" name="nama_perusahaan" class="form-control form-control-solid" value="{{ old('nama_perusahaan') }}" placeholder="Nama perusahaan" required></div>
                        <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-create-email">Email</label><input id="company-create-email" type="email" name="email_perusahaan" class="form-control form-control-solid" value="{{ old('email_perusahaan') }}" placeholder="nama@perusahaan.co.id" required></div>
                        <div class="col-12"><label class="required fs-6 fw-semibold mb-2" for="company-create-address">Alamat</label><textarea id="company-create-address" name="alamat_perusahaan" class="form-control form-control-solid" rows="3" placeholder="Alamat lengkap perusahaan" required>{{ old('alamat_perusahaan') }}</textarea></div>
                        <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-create-phone">Nomor Telepon</label><input id="company-create-phone" type="tel" name="tlp_perusahaan" class="form-control form-control-solid" value="{{ old('tlp_perusahaan') }}" placeholder="Nomor telepon" required></div>
                        <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-create-npwp">NPWP</label><input id="company-create-npwp" type="text" name="no_npwp" class="form-control form-control-solid" value="{{ old('no_npwp') }}" placeholder="Nomor NPWP" required></div>
                        <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-create-director">Nama Pimpinan</label><input id="company-create-director" type="text" name="nama_direktur_perusahaan" class="form-control form-control-solid" value="{{ old('nama_direktur_perusahaan') }}" placeholder="Nama direktur/pimpinan" required></div>
                        <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-create-position">Jabatan Pimpinan</label><input id="company-create-position" type="text" name="jabatan_perusahaan" class="form-control form-control-solid" value="{{ old('jabatan_perusahaan') }}" placeholder="Jabatan" required></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-6 px-lg-8 pb-6">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Perusahaan</button>
                </div>
            </form>
        </div>
    </div>
</div>
