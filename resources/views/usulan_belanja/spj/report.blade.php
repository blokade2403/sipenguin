<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan SPJ {{ $spj->no_usulan_belanja }}</title>
    <style>
        @page { margin: 28px 34px 38px; }
        body { font-family: DejaVu Sans, sans-serif; color: #263449; font-size: 9px; line-height: 1.45; }
        .header { text-align: center; border-bottom: 3px double #263449; padding-bottom: 10px; margin-bottom: 18px; }
        .header .line { font-size: 10px; }
        .header .name { font-size: 15px; font-weight: bold; margin: 3px 0; }
        h1 { text-align: center; font-size: 14px; margin: 14px 0 2px; }
        .subtitle { text-align: center; color: #64748b; margin-bottom: 16px; }
        h2 { font-size: 10px; color: #135b9b; border-bottom: 1px solid #cbd8e6; padding-bottom: 5px; margin: 16px 0 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 9px; }
        th, td { border: 1px solid #dbe3ec; padding: 5px 6px; vertical-align: top; }
        th { background: #eff5fb; text-align: left; font-weight: bold; }
        .plain td { border: 0; padding: 3px 5px; }
        .label { width: 23%; color: #64748b; }
        .value { width: 27%; font-weight: bold; }
        .right { text-align: right; white-space: nowrap; }
        .muted { color: #64748b; }
        .status { font-weight: bold; }
        .photos { margin: 4px 0 10px; }
        .photos img { width: 145px; height: 110px; object-fit: contain; border: 1px solid #dbe3ec; padding: 3px; margin-right: 6px; }
        .note { padding: 8px; border: 1px solid #dbe3ec; background: #f8fafc; }
        .footer { position: fixed; bottom: -22px; left: 0; right: 0; text-align: right; color: #8491a3; font-size: 8px; }
        .total td { font-weight: bold; background: #f2f7fc; }
        .small { font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="line">{{ $header->header1 ?? '' }}</div>
        <div class="line"><strong>{{ $header->header4 ?? '' }}</strong></div>
        <div class="name">{{ $header->nama_rs ?? '' }}</div>
        <div class="line">{{ $header->alamat_rs ?? '' }}</div>
        <div class="line">Telp. {{ $header->tlp_rs ?? '' }} &nbsp; | &nbsp; {{ $header->email_rs ?? '' }}</div>
    </div>

    <h1>LAPORAN PERTANGGUNGJAWABAN SPJ</h1>
    <div class="subtitle">Ringkasan data usulan belanja, pengadaan, penerimaan barang, verifikasi, dan pembayaran</div>

    <h2>A. Identitas Usulan dan SPJ</h2>
    <table class="plain">
        <tr><td class="label">Nomor usulan</td><td class="value">{{ $usulan?->no_usulan_belanja ?? $spj->no_usulan_belanja }}</td><td class="label">Nomor SPJ</td><td class="value">{{ $spj->no_usulan_belanja }}-{{ str_pad((string) $spj->nomor_urut, 2, '0', STR_PAD_LEFT) }}</td></tr>
        <tr><td class="label">Pengusul</td><td class="value">{{ $usulan?->nama_pengusul ?? '-' }}</td><td class="label">Unit kerja</td><td class="value">{{ $usulan?->unit?->nama_unit ?? '-' }}</td></tr>
        <tr><td class="label">Penyedia</td><td class="value">{{ $providerName ?: '-' }}</td><td class="label">Tahun anggaran</td><td class="value">{{ $usulan?->tahunAnggaran?->nama_tahun_anggaran ?? $master->tahun_anggaran ?? '-' }}</td></tr>
        <tr><td class="label">Status SPJ</td><td class="value">{{ $spj->status_spj ?: '-' }}</td><td class="label">Tahap pengadaan</td><td class="value">{{ $panelData['steps'][$spj->pengadaan_step] ?? $spj->pengadaan_step ?? '-' }}</td></tr>
    </table>

    <h2>B. Informasi Program, RKBU, dan Sumber Dana</h2>
    <table class="plain">
        <tr><td class="label">Program</td><td class="value" colspan="3">{{ $program?->kode_program }} {{ $program?->nama_program ?? '-' }}</td></tr>
        <tr><td class="label">Kegiatan</td><td class="value" colspan="3">{{ $kegiatan?->kode_kegiatan }} {{ $kegiatan?->nama_kegiatan ?? '-' }}</td></tr>
        <tr><td class="label">Subkegiatan</td><td class="value" colspan="3">{{ $rkbu?->subKegiatan?->kode_sub_kegiatan }} {{ $rkbu?->subKegiatan?->nama_sub_kegiatan ?? '-' }}</td></tr>
        <tr><td class="label">Rekening belanja</td><td class="value" colspan="3">{{ $rekeningBelanja?->kode_rekening_belanja }} {{ $rekeningBelanja?->nama_rekening_belanja ?? '-' }}</td></tr>
        <tr><td class="label">Subkategori RKBU</td><td class="value">{{ $rkbu?->subKategoriRkbu?->kode_sub_kategori_rkbu }} {{ $rkbu?->subKategoriRkbu?->nama_sub_kategori_rkbu ?? '-' }}</td><td class="label">Sumber dana</td><td class="value">{{ $sumberDana?->nama_sumber_dana ?? '-' }}</td></tr>
    </table>

    <h2>C. Rincian Barang dan Nilai SPJ</h2>
    <table>
        <thead><tr><th style="width:4%">No.</th><th style="width:19%">Nama barang</th><th style="width:26%">Spesifikasi</th><th style="width:12%">Nomor RKBU</th><th style="width:9%">Volume</th><th style="width:14%">Harga satuan</th><th style="width:16%">Total + PPN</th></tr></thead>
        <tbody>
            @forelse ($items as $item)
                <tr><td>{{ $loop->iteration }}</td><td>{{ $item->nama_barang ?? '-' }}</td><td>{{ $item->spesifikasi ?: '-' }}</td><td>{{ $item->nomor_rkbu ?? '-' }}</td><td>{{ $item->volume }} {{ $item->satuan_1 }}</td><td class="right">Rp {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}</td><td class="right">Rp {{ number_format((float) $item->total_anggaran + (float) $item->total_ppn, 0, ',', '.') }}</td></tr>
            @empty
                <tr><td colspan="7" class="muted">Belum ada rincian item.</td></tr>
            @endforelse
            <tr><td colspan="5"></td><td>Subtotal</td><td class="right">Rp {{ number_format($netto, 0, ',', '.') }}</td></tr>
            <tr><td colspan="5"></td><td>PPN</td><td class="right">Rp {{ number_format($ppn, 0, ',', '.') }}</td></tr>
            <tr class="total"><td colspan="5"></td><td>Total anggaran</td><td class="right">Rp {{ number_format($total, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>
    @if (filled($spj->rincian_belanja))
        <div class="note"><strong>Rincian belanja pada surat pesanan:</strong><br>{{ $spj->rincian_belanja }}</div>
    @endif

    <h2>D. Surat Pesanan</h2>
    <table class="plain">
        <tr><td class="label">Nomor surat pesanan</td><td class="value">{{ $spj->no_surat_pesanan ?: '-' }}</td><td class="label">Tanggal surat pesanan</td><td class="value">{{ $spj->tgl_surat_pesanan ? \Illuminate\Support\Carbon::parse($spj->tgl_surat_pesanan)->translatedFormat('d F Y') : '-' }}</td></tr>
        <tr><td class="label">ID Paket</td><td class="value">{{ $spj->idpaket ?: ($master->idpaket ?? '-') }}</td><td class="label">Nomor SPK</td><td class="value">{{ $master->nomor_spk ?? '-' }}</td></tr>
        <tr><td class="label">Batas waktu pekerjaan</td><td class="value" colspan="3">{{ $master->jangka_waktu_pekerjaan ?? '-' }}</td></tr>
    </table>

    <h2>E. Penerimaan dan Pemeriksaan Barang</h2>
    <table class="plain">
        <tr><td class="label">Catatan barang datang</td><td class="value">{{ $spj->keterangan_barang_datang ?: 'Tidak ada catatan.' }}</td><td class="label">Tanggal datang terakhir</td><td class="value">{{ $spj->tgl_barang_datang ? \Illuminate\Support\Carbon::parse($spj->tgl_barang_datang)->translatedFormat('d F Y') : '-' }}</td></tr>
        <tr><td class="label">Status pengurus barang</td><td class="value">{{ $spj->status_verifikasi_pengurus_barang ?: 'Menunggu' }}</td><td class="label">Tanggal verifikasi</td><td class="value">{{ $spj->tgl_verif_pengurus_barang ? \Illuminate\Support\Carbon::parse($spj->tgl_verif_pengurus_barang)->translatedFormat('d F Y') : '-' }}</td></tr>
        <tr><td class="label">Catatan verifikasi</td><td class="value" colspan="3">{{ $spj->keterangan_verif_pengurus_barang ?: '-' }}</td></tr>
        <tr><td class="label">Nomor BAST serah terima unit</td><td class="value">{{ $spj->no_bast_serah_terima_unit ?: '-' }}</td><td class="label">Penerima barang</td><td class="value">{{ $spj->nama_penerima_barang ?: '-' }}</td></tr>
    </table>
    @if ($receipts->isNotEmpty())
        <table><thead><tr><th style="width:16%">Tanggal datang</th><th style="width:15%">Status</th><th>Keterangan</th></tr></thead><tbody>
            @foreach ($receipts as $receipt)
                <tr><td>{{ \Illuminate\Support\Carbon::parse($receipt->tanggal_datang)->translatedFormat('d F Y') }}</td><td>{{ $receipt->status_penerimaan === 'selesai' ? 'Selesai' : 'Parsial' }}</td><td>{{ $receipt->keterangan ?: '-' }}</td></tr>
            @endforeach
        </tbody></table>
    @endif
    @if ($photos->isNotEmpty())
        <div class="photos">
            @foreach ($photos as $photo)
                <img src="{{ $photo['path'] }}" alt="Foto barang datang">
            @endforeach
        </div>
    @endif

    <h2>F. Data BAST dan Tukar Faktur</h2>
    <table class="plain">
        <tr><td class="label">Tanggal BAST</td><td class="value">{{ $spj->tgl_bast ? \Illuminate\Support\Carbon::parse($spj->tgl_bast)->translatedFormat('d F Y') : '-' }}</td><td class="label">Nomor BA pemeriksaan</td><td class="value">{{ $spj->no_ba ?: '-' }}</td></tr>
        <tr><td class="label">Nomor BA serah terima hasil</td><td class="value">{{ $spj->no_ba_hp ?: '-' }}</td><td class="label">Nomor BA serah terima barang</td><td class="value">{{ $spj->no_ba_bp ?: '-' }}</td></tr>
        <tr><td class="label">Nomor DPA</td><td class="value">{{ $spj->no_dpa ?: '-' }}</td><td class="label">Status tukar faktur</td><td class="value">{{ $master->status_proses_tukar_faktur ?? 'Menunggu' }}</td></tr>
        <tr><td class="label">Tanggal faktur</td><td class="value">{{ $master->tanggal_faktur ?? '-' }}</td><td class="label">Tanggal kwitansi</td><td class="value">{{ $master->tgl_kwitansi ?? '-' }}</td></tr>
        <tr><td class="label">Nomor faktur</td><td class="value" colspan="3">{{ $master->no_faktur ?? '-' }}</td></tr>
    </table>

    <h2>G. Status Persetujuan Dokumen</h2>
    <table><thead><tr><th style="width:7%">Tahap</th><th>Validator</th><th style="width:18%">Status</th><th style="width:25%">Tanggal tindakan</th></tr></thead><tbody>
        @forelse ($checklist as $stage)
            <tr><td>{{ $stage['order'] ?? $loop->iteration }}</td><td>{{ $stage['name'] }}</td><td>{{ $stage['status'] ?? ($stage['done'] ? 'Selesai' : 'Menunggu') }}</td><td>{{ $stage['acted_at'] ? \Illuminate\Support\Carbon::parse($stage['acted_at'])->translatedFormat('d F Y, H:i') : '-' }}</td></tr>
        @empty
            <tr><td colspan="4">Data checklist workflow tidak tersedia.</td></tr>
        @endforelse
    </tbody></table>
    <table class="plain">
        <tr><td class="label">Status Direktur</td><td class="value">{{ $master->status_verifikasi_direktur ?? 'Selesai' }}</td><td class="label">Tanggal verifikasi Direktur</td><td class="value">{{ $master->tgl_verif_direktur ?? '-' }}</td></tr>
        <tr><td class="label">Catatan Direktur</td><td class="value" colspan="3">{{ $master->ket_verif_direktur ?? '-' }}</td></tr>
    </table>

    <h2>H. Penyerahan dan Pembayaran</h2>
    <table class="plain">
        <tr><td class="label">Status serah terima Bendahara</td><td class="value">{{ $master->status_serah_terima_bendahara ?? 'Menunggu' }}</td><td class="label">Tanggal penyerahan SPJ</td><td class="value">{{ $master->tanggal_penyerahan_spj ?? '-' }}</td></tr>
        <tr><td class="label">Bruto</td><td class="value">Rp {{ number_format((float) ($master->bruto ?? $total), 0, ',', '.') }}</td><td class="label">Harga dasar</td><td class="value">Rp {{ number_format((float) ($master->harga_dasar ?? $netto), 0, ',', '.') }}</td></tr>
        <tr><td class="label">Status pembayaran</td><td class="value">{{ $master->status_pembayaran ?? '-' }}</td><td class="label">Total pembayaran</td><td class="value">Rp {{ number_format((float) ($master->pembayaran ?? 0), 0, ',', '.') }}</td></tr>
        <tr><td class="label">Tanggal pembayaran</td><td class="value">{{ $master->tanggal_pembayaran ?? '-' }}</td><td class="label">Sisa pembayaran</td><td class="value">Rp {{ number_format((float) ($master->sisa_pembayaran ?? max(0, $total - (float) ($master->pembayaran ?? 0))), 0, ',', '.') }}</td></tr>
    </table>
    @if ($payments->isNotEmpty())
        <table><thead><tr><th style="width:22%">Tanggal pembayaran</th><th>Jumlah</th><th style="width:24%">Bulan</th><th style="width:28%">Bendahara</th></tr></thead><tbody>
            @foreach ($payments as $payment)
                <tr><td>{{ $payment->tanggal_pembayaran ? \Illuminate\Support\Carbon::parse($payment->tanggal_pembayaran)->translatedFormat('d F Y') : '-' }}</td><td class="right">Rp {{ number_format((float) $payment->amount, 0, ',', '.') }}</td><td>{{ $payment->bulan_pembayaran ?? '-' }}</td><td>{{ $payment->nama_bendahara ?? '-' }}</td></tr>
            @endforeach
        </tbody></table>
    @endif

    <h2>I. Riwayat Proses</h2>
    <table><thead><tr><th style="width:20%">Waktu</th><th style="width:24%">Tahap</th><th style="width:18%">Petugas</th><th>Catatan</th></tr></thead><tbody>
        @forelse ($logs as $log)
            <tr><td>{{ $log->created_at ? \Illuminate\Support\Carbon::parse($log->created_at)->translatedFormat('d F Y, H:i') : '-' }}</td><td>{{ $log->aksi }}</td><td>{{ $log->nama_user ?? '-' }}</td><td>{{ $log->catatan ?: '-' }}</td></tr>
        @empty
            <tr><td colspan="4">Belum ada catatan riwayat proses.</td></tr>
        @endforelse
    </tbody></table>

    <div class="footer">Dicetak {{ now()->translatedFormat('d F Y, H:i') }} &nbsp; | &nbsp; {{ $spj->no_usulan_belanja }}-{{ str_pad((string) $spj->nomor_urut, 2, '0', STR_PAD_LEFT) }}</div>
</body>
</html>
