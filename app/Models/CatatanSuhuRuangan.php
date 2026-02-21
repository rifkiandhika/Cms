<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatatanSuhuRuangan extends Model
{
    use SoftDeletes;

    protected $table = 'catatan_suhu_ruangans';

    protected $fillable = [
        'kontrol_gudang_id',
        'tanggal',
        'kebersihan',
        'suhu_refrigerator',
        'suhu_ruangan',
        'kelembapan',
        'keamanan',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'kebersihan' => 'boolean',
        'keamanan'   => 'boolean',
    ];

    public function kontrolGudang()
    {
        return $this->belongsTo(KontrolGudang::class, 'kontrol_gudang_id');
    }
}