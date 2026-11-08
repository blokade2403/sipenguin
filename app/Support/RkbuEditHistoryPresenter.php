<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class RkbuEditHistoryPresenter
{
    private const LABELS = [
        'created_by' => 'Nama pengusul',
        'sumber_dana_id' => 'Sumber dana',
        'sub_kegiatan_id' => 'Sub kegiatan',
        'sub_kategori_rkbu_id' => 'Sub kategori RKBU',
        'nama_barang' => 'Nama barang/pegawai',
        'spesifikasi' => 'Spesifikasi',
        'rating' => 'Rating',
        'satuan_1' => 'Satuan 1',
        'satuan_2' => 'Satuan 2',
        'volume_1' => 'Volume 1',
        'volume_2' => 'Volume 2',
        'jumlah_volume' => 'Jumlah volume',
        'harga_satuan' => 'Harga satuan',
        'ppn' => 'PPN (%)',
        'total_anggaran' => 'Total anggaran',
        'sisa_vol_rkbu' => 'Sisa volume RKBU',
        'sisa_anggaran_rkbu' => 'Sisa anggaran RKBU',
        'kondisi_baik' => 'Kondisi baik',
        'kondisi_rusak_berat' => 'Kondisi rusak berat',
        'eksisting' => 'Eksisting',
        'penempatan' => 'Penempatan',
        'standar_kebutuhan' => 'Standar kebutuhan',
        'keterangan_status' => 'Keterangan status',
        'link_ekatalog' => 'Link e-katalog',
    ];

    public static function changes(?array $before, ?array $after): array
    {
        $before ??= [];
        $after ??= [];
        $changes = [];
        $beforeHeader = $before['header'] ?? [];
        $afterHeader = $after['header'] ?? [];
        foreach (array_unique(array_merge(array_keys($beforeHeader), array_keys($afterHeader))) as $field) {
            self::appendChange($changes, 'Data RKBU', $field, $beforeHeader[$field] ?? null, $afterHeader[$field] ?? null);
        }

        $beforeDetails = collect($before['details'] ?? [])->keyBy('id');
        $afterDetails = collect($after['details'] ?? [])->keyBy('id');
        foreach ($beforeDetails->keys()->merge($afterDetails->keys())->unique() as $id) {
            $old = $beforeDetails->get($id, []);
            $new = $afterDetails->get($id, []);
            $oldFields = $old['fields'] ?? [];
            $newFields = $new['fields'] ?? [];
            $name = $newFields['nama_barang'] ?? $newFields['nama_pegawai'] ?? $oldFields['nama_barang'] ?? $oldFields['nama_pegawai'] ?? 'Item RKBU';
            foreach (array_unique(array_merge(array_keys($oldFields), array_keys($newFields))) as $field) {
                self::appendChange($changes, $name, $field, $oldFields[$field] ?? null, $newFields[$field] ?? null);
            }
        }

        return $changes;
    }

    public static function comparisonRows(?array $before, ?array $after): array
    {
        $before ??= [];
        $after ??= [];
        $oldDetails = collect($before['details'] ?? [])->keyBy('id');
        $newDetails = collect($after['details'] ?? [])->keyBy('id');
        $beforeHeader = $before['header'] ?? [];
        $afterHeader = $after['header'] ?? [];
        $rows = [];

        foreach ($oldDetails->keys()->merge($newDetails->keys())->unique() as $id) {
            $old = $oldDetails->get($id, []);
            $new = $newDetails->get($id, []);
            $oldFields = $old['fields'] ?? [];
            $newFields = $new['fields'] ?? [];
            if ($oldFields === $newFields && $beforeHeader === $afterHeader) {
                continue;
            }
            $name = $newFields['nama_barang'] ?? $newFields['nama_pegawai'] ?? $oldFields['nama_barang'] ?? $oldFields['nama_pegawai'] ?? 'Item RKBU';
            $rows[] = [
                'name' => $name,
                'id' => $id,
                'category_before' => self::readableValue('sub_kategori_rkbu_id', $beforeHeader['sub_kategori_rkbu_id'] ?? null),
                'category_after' => self::readableValue('sub_kategori_rkbu_id', $afterHeader['sub_kategori_rkbu_id'] ?? null),
                'before' => self::displayFields($oldFields),
                'after' => self::displayFields($newFields),
                'delta' => self::number($newFields['total_anggaran'] ?? 0) - self::number($oldFields['total_anggaran'] ?? 0),
            ];
        }

        if (! $rows && $beforeHeader !== $afterHeader) {
            $rows[] = [
                'name' => 'Perubahan data RKBU', 'id' => null,
                'category_before' => self::readableValue('sub_kategori_rkbu_id', $beforeHeader['sub_kategori_rkbu_id'] ?? null),
                'category_after' => self::readableValue('sub_kategori_rkbu_id', $afterHeader['sub_kategori_rkbu_id'] ?? null),
                'before' => [], 'after' => [], 'delta' => 0,
            ];
        }

        return $rows;
    }

    private static function displayFields(array $fields): array
    {
        $volume = collect([
            trim(($fields['volume_1'] ?? '') . ' ' . ($fields['satuan_1'] ?? '')),
            trim(($fields['volume_2'] ?? '') . ' ' . ($fields['satuan_2'] ?? '')),
        ])->filter(fn ($part) => trim($part) !== '')->implode(' × ');

        return [
            'specification' => $fields['spesifikasi'] ?? null,
            'volume' => $volume ?: ($fields['jumlah_volume'] ?? null),
            'unit_price' => $fields['harga_satuan'] ?? null,
            'total' => $fields['total_anggaran'] ?? null,
        ];
    }

    private static function number(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    private static function appendChange(array &$changes, string $item, string $field, mixed $before, mixed $after): void
    {
        if ((string) ($before ?? '') === (string) ($after ?? '')) {
            return;
        }

        $oldNumber = is_numeric($before) ? (float) $before : null;
        $newNumber = is_numeric($after) ? (float) $after : null;
        $before = self::readableValue($field, $before);
        $after = self::readableValue($field, $after);
        $changes[] = [
            'item' => $item,
            'field' => self::LABELS[$field] ?? ucfirst(str_replace('_', ' ', $field)),
            'before' => $before,
            'after' => $after,
            'difference' => $oldNumber !== null && $newNumber !== null ? $newNumber - $oldNumber : null,
            'numeric' => $oldNumber !== null && $newNumber !== null,
        ];
    }

    private static function readableValue(string $field, mixed $value): mixed
    {
        if (! $value) {
            return $value;
        }

        [$table, $key, $label] = match ($field) {
            'created_by' => ['users', 'id', 'nama'],
            'sumber_dana_id' => ['sumber_danas', 'id_sumber_dana', 'nama_sumber_dana'],
            'sub_kegiatan_id' => ['sub_kegiatans', 'id_sub_kegiatan', 'nama_sub_kegiatan'],
            'sub_kategori_rkbu_id' => ['sub_kategori_rkbus', 'id_sub_kategori_rkbu', 'nama_sub_kategori_rkbu'],
            default => [null, null, null],
        };

        if (! $table) {
            return $value;
        }

        return DB::table($table)->where($key, $value)->value($label) ?: $value;
    }
}
