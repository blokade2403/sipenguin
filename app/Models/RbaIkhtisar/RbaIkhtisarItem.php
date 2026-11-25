<?php

namespace App\Models\RbaIkhtisar;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RbaIkhtisarItem extends Model
{
    use HasUuids;

    protected $table = 'rba_ikhtisar_items';

    protected $fillable = [
        'section', 'urutan', 'kode_ikhtisar', 'nama_ikhtisar', 'jenis_baris', 'level', 'is_active',
    ];

    public function mappings(): HasMany
    {
        return $this->hasMany(RbaIkhtisarMapping::class, 'ikhtisar_item_id');
    }

    public function realisasis(): HasMany
    {
        return $this->hasMany(RbaIkhtisarRealisasi::class, 'ikhtisar_item_id');
    }
}
