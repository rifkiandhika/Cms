<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KontrolGudang extends Model
{
    use SoftDeletes;

    protected $table = 'kontrol_gudangs';

    protected $fillable = [
        'periode',
        'nama_gudang',
    ];

    public function catatanSuhu()
    {
        return $this->hasMany(CatatanSuhuRuangan::class, 'kontrol_gudang_id');
    }
}