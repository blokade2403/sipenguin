<?php

namespace App\Models\RbaIkhtisar;

use App\Models\MasterBackend\SettingRkbu\SubKategoriRkbu;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RbaIkhtisarMapping extends Model
{
    use HasUuids;

    protected $table = 'rba_ikhtisar_mappings';

    protected $fillable = ['ikhtisar_item_id', 'sub_kategori_rkbu_id', 'rekening_pendapatan_id', 'jenis_sumber_dana'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(RbaIkhtisarItem::class, 'ikhtisar_item_id');
    }

    public function subkategori(): BelongsTo
    {
        return $this->belongsTo(SubKategoriRkbu::class, 'sub_kategori_rkbu_id', 'id_sub_kategori_rkbu');
    }
}
