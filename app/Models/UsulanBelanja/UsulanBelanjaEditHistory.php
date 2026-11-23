<?php

namespace App\Models\UsulanBelanja;

use App\Models\MasterBackend\UserProfil\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsulanBelanjaEditHistory extends Model
{
    use HasUuids;

    protected $table = 'usulan_belanja_edit_histories';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['usulan_belanja_id', 'user_id', 'aksi', 'sebelum', 'sesudah'];

    protected $casts = ['sebelum' => 'array', 'sesudah' => 'array'];

    public function usulanBelanja(): BelongsTo
    {
        return $this->belongsTo(UsulanBelanja::class, 'usulan_belanja_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
