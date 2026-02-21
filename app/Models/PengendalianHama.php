<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengendalianHama extends Model
{
    use SoftDeletes;

    protected $table = 'pengendalian_hamas';

    protected $fillable = [
        'lokasi',
        'bulan',
        'tahun',
        'penanggung_jawab',
        'paraf_pj',
    ];

    public function details()
    {
        return $this->hasMany(DetailPengendalianHama::class, 'pengendalian_hama_id');
    }

    public function gambar()
    {
        return $this->hasMany(PengendalianHamaGambar::class, 'pengendalian_hama_id');
    }
}