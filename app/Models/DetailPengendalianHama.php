<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPengendalianHama extends Model
{
    use SoftDeletes;

    protected $table = 'detail_pengendalian_hamas';

    protected $fillable = [
        'pengendalian_hama_id',
        'tanggal',
        'hari',
        'waktu',
        'treatment_c',
        'treatment_b',
        'treatment_f',
        'treatment_i',
        'perangkap_perlakuan',
        'jumlah_hama',
        'evaluasi',
        'nama_petugas',
        'paraf_petugas',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'treatment_c'   => 'boolean',
        'treatment_b'   => 'boolean',
        'treatment_f'   => 'boolean',
        'treatment_i'   => 'boolean',
        'paraf_petugas' => 'boolean',
    ];

    public function pengendalianHama()
    {
        return $this->belongsTo(PengendalianHama::class, 'pengendalian_hama_id');
    }
}