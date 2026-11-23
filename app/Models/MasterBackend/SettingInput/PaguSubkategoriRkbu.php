<?php

namespace App\Models\MasterBackend\SettingInput;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PaguSubkategoriRkbu extends Model
{
    use HasUuids;

    protected $table = 'pagu_subkategori_rkbus';
    protected $fillable = ['tahun_anggaran_id', 'sub_kategori_rkbu_id', 'jenis_sumber_dana', 'jumlah_pagu'];
}
