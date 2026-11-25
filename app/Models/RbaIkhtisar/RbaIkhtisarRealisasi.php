<?php

namespace App\Models\RbaIkhtisar;

use App\Models\MasterBackend\SettingInput\TahunAnggaran;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RbaIkhtisarRealisasi extends Model
{
    use HasUuids;

    protected $table = 'rba_ikhtisar_realisasis';

    protected $fillable = [
        'tahun_anggaran_id', 'ikhtisar_item_id', 'jenis_sumber_dana', 'jumlah_realisasi', 'keterangan_sumber', 'created_by',
    ];

    protected function casts(): array
    {
        return ['jumlah_realisasi' => 'decimal:2'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(RbaIkhtisarItem::class, 'ikhtisar_item_id');
    }

    public function tahunAnggaran(): BelongsTo
    {
        return $this->belongsTo(TahunAnggaran::class, 'tahun_anggaran_id');
    }
}
