@extends('layouts.main')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Riwayat Perubahan RKBU · {{ $title }}</h3>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2 mb-5">
                <a href="{{ route('workflowapproval.rkbu-history') }}" class="btn btn-sm {{ $module === null ? 'btn-primary' : 'btn-light-primary' }}">Semua Modul</a>
                @foreach ($modules as $key => $item)
                    <a href="{{ route('workflowapproval.rkbu-history.module', $key) }}" class="btn btn-sm {{ $module === $key ? 'btn-primary' : 'btn-light-primary' }}">{{ $item['label'] }}</a>
                @endforeach
            </div>
            <div class="table-responsive">
                <table id="rkbu-history-table" class="table table-row-bordered align-middle gy-4">
                    <thead>
                        <tr>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Nama Barang</th>
                            <th colspan="3" class="text-center bg-light">Sebelum</th>
                            <th colspan="3" class="text-center bg-success-subtle">Sesudah</th>
                        </tr>
                        <tr>
                            <th>Detail Spesifikasi</th><th>Vol</th><th>Detail Harga</th>
                            <th>Detail Spesifikasi</th><th>Vol</th><th>Detail Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($rowNumber = 0)
                        @foreach ($history as $entry)
                            @foreach ($entry->comparisonRows as $row)
                                @php($rowNumber++)
                                <tr>
                                    <td>{{ $rowNumber }}</td>
                                    <td style="min-width: 280px">
                                        <strong class="text-primary">{{ $row['name'] }}</strong>
                                        <div class="small text-muted">{{ $row['id'] ?? $entry->rkbu?->nomor_rkbu ?? '-' }}</div>
                                        <div class="small text-muted">{{ $entry->created_at?->format('Y-m-d H:i:s') }}</div>
                                        <div class="small mt-2">Kategori: {{ $row['category_after'] ?: $row['category_before'] ?: '—' }}</div>
                                        <div class="small">Diubah oleh: {{ $entry->user?->nama ?? '-' }}</div>
                                        @if ($entry->alasan)
                                            <div class="small mt-1">Keterangan: {{ $entry->alasan }}</div>
                                        @endif
                                        @if ($entry->lampiran)
                                            <a class="small d-inline-block mt-1" href="{{ asset('storage/' . $entry->lampiran) }}" target="_blank" rel="noopener">Dok. Pendukung</a>
                                        @endif
                                    </td>
                                    @foreach (['before', 'after'] as $side)
                                        @php($category = $side === 'before' ? $row['category_before'] : $row['category_after'])
                                        @php($values = $row[$side])
                                        <td style="min-width: 220px">
                                            @if ($category)<strong>Sub Kategori: {{ $category }}</strong><br>@endif
                                            {{ $values['specification'] ?: '—' }}
                                        </td>
                                        <td style="min-width: 110px">{{ $values['volume'] ?: '—' }}</td>
                                        <td style="min-width: 145px">
                                            Harga Satuan Rp. {{ isset($values['unit_price']) && is_numeric($values['unit_price']) ? number_format((float) $values['unit_price'], 0, ',', '.') : '—' }}
                                            <div><span class="badge bg-dark mt-1">Rp. {{ isset($values['total']) && is_numeric($values['total']) ? number_format((float) $values['total'], 0, ',', '.') : '0' }}</span></div>
                                            @if ($side === 'after')
                                                <div><span class="badge border border-danger text-danger mt-2">Selisih: Rp. {{ number_format((float) $row['delta'], 0, ',', '.') }}</span></div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#rkbu-history-table').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50],
                order: [],
                language: {
                    lengthMenu: 'Tampilkan _MENU_ data per halaman',
                    search: 'Cari:',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ perubahan',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 perubahan',
                    zeroRecords: 'Tidak ada data yang cocok',
                    emptyTable: 'Belum ada riwayat perubahan RKBU.',
                    paginate: { first: 'Pertama', last: 'Terakhir', next: 'Berikutnya', previous: 'Sebelumnya' }
                }
            });
        });
    </script>
@endpush
