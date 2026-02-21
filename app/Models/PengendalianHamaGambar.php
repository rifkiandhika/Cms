<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengendalianHamaGambar extends Model
{
    protected $table = 'pengendalian_hama_gambar';

    protected $fillable = [
        'pengendalian_hama_id',
        'path_gambar',
        'nama_file',
    ];

    public function pengendalianHama()
    {
        return $this->belongsTo(PengendalianHama::class, 'pengendalian_hama_id');
    }
}