@foreach ($perusahaans as $item)
    <div class="modal fade" id="companyEditModal{{ $item->id_perusahaan }}" tabindex="-1" aria-labelledby="companyEditModalLabel{{ $item->id_perusahaan }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-3">
                <div class="modal-header border-0 pb-0 px-6 px-lg-8 pt-6">
                    <div class="d-flex align-items-center gap-3">
                        <span class="company-modal-icon"><i class="fas fa-pen"></i></span>
                        <div><h2 class="modal-title fs-3 fw-bold text-gray-900" id="companyEditModalLabel{{ $item->id_perusahaan }}">Edit Perusahaan</h2><div class="text-muted fs-7">Perbarui informasi {{ $item->nama_perusahaan }}.</div></div>
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal" aria-label="Tutup"><i class="fas fa-times"></i></button>
                </div>
                <form action="{{ route('perusahaans.update', $item->id_perusahaan) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body px-6 px-lg-8 py-6">
                        <div class="row g-5">
                            <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-edit-name-{{ $item->id_perusahaan }}">Nama Perusahaan</label><input id="company-edit-name-{{ $item->id_perusahaan }}" type="text" name="nama_perusahaan" class="form-control form-control-solid" value="{{ $item->nama_perusahaan }}" required></div>
                            <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-edit-email-{{ $item->id_perusahaan }}">Email</label><input id="company-edit-email-{{ $item->id_perusahaan }}" type="email" name="email_perusahaan" class="form-control form-control-solid" value="{{ $item->email_perusahaan }}" required></div>
                            <div class="col-12"><label class="required fs-6 fw-semibold mb-2" for="company-edit-address-{{ $item->id_perusahaan }}">Alamat</label><textarea id="company-edit-address-{{ $item->id_perusahaan }}" name="alamat_perusahaan" class="form-control form-control-solid" rows="3" required>{{ $item->alamat_perusahaan }}</textarea></div>
                            <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-edit-phone-{{ $item->id_perusahaan }}">Nomor Telepon</label><input id="company-edit-phone-{{ $item->id_perusahaan }}" type="tel" name="tlp_perusahaan" class="form-control form-control-solid" value="{{ $item->tlp_perusahaan }}" required></div>
                            <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-edit-npwp-{{ $item->id_perusahaan }}">NPWP</label><input id="company-edit-npwp-{{ $item->id_perusahaan }}" type="text" name="no_npwp" class="form-control form-control-solid" value="{{ $item->no_npwp }}" required></div>
                            <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-edit-director-{{ $item->id_perusahaan }}">Nama Pimpinan</label><input id="company-edit-director-{{ $item->id_perusahaan }}" type="text" name="nama_direktur_perusahaan" class="form-control form-control-solid" value="{{ $item->nama_direktur_perusahaan }}" required></div>
                            <div class="col-md-6"><label class="required fs-6 fw-semibold mb-2" for="company-edit-position-{{ $item->id_perusahaan }}">Jabatan Pimpinan</label><input id="company-edit-position-{{ $item->id_perusahaan }}" type="text" name="jabatan_perusahaan" class="form-control form-control-solid" value="{{ $item->jabatan_perusahaan }}" required></div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-6 px-lg-8 pb-6">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
