<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationForm extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'sop_id',
        'materi_pelatihan',
        'hari_tanggal',
        'tempat_pelatihan',
        'nama_peserta',
        'jabatan_lokasi_kerja',
        'kompetensi_diharapkan',
        'perilaku_sebelum_training',
        'perilaku_setelah_training',
        'pendapat_efektif',
        'signature_atasan',
        'signature_personalia',
    ];

    protected $casts = [
        'hari_tanggal' => 'date',
    ];
}