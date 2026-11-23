@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-header"><h3 class="card-title">Riwayat Perubahan Usulan Belanja</h3></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-bordered align-middle gy-4">
                    <thead><tr><th>Waktu</th><th>Nomor Usulan</th><th>Aksi</th><th>Perubahan</th><th>Diubah oleh</th></tr></thead>
                    <tbody>
                        @forelse ($history as $entry)
                            @php($before = $entry->sebelum ?? [])
                            @php($after = $entry->sesudah ?? [])
                            @php($values = $after ?: $before)
                            @php($typeLabel = match ($values['detail_type'] ?? '') { 'barjas' => 'Barjas', 'persediaan' => 'Persediaan', 'modal' => 'Modal Kantor', 'modal_alkes' => 'Modal Alkes', 'pegawai' => 'Pegawai', default => 'RKBU' })
                            <tr>
                                <td class="text-nowrap">{{ $entry->created_at?->format('d-m-Y H:i:s') }}</td>
                                <td>{{ $entry->usulanBelanja?->no_usulan_belanja ?? $entry->usulan_belanja_id }}</td>
                                <td><span class="badge badge-light-primary">Item {{ ucfirst($entry->aksi) }}</span></td>
                                <td style="min-width: 360px">
                                    <div class="fw-bold">{{ $typeLabel }}</div>
                                    <div class="small">Spesifikasi: {{ $before['spesifikasi'] ?? '—' }} → {{ $after['spesifikasi'] ?? '—' }}</div>
                                    <div class="small text-muted">Volume: {{ $before['volume'] ?? '—' }} → {{ $after['volume'] ?? '—' }} · Harga: Rp {{ number_format((float) ($before['harga_satuan'] ?? 0), 0, ',', '.') }} → Rp {{ number_format((float) ($after['harga_satuan'] ?? 0), 0, ',', '.') }} · PPN: {{ $before['ppn'] ?? '—' }}% → {{ $after['ppn'] ?? '—' }}%</div>
                                </td>
                                <td>{{ $entry->user?->nama ?? 'Akun dihapus' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-8">Belum ada riwayat perubahan Usulan Belanja.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $history->links() }}
        </div>
    </div>
@endsection
