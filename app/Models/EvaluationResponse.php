<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_program_id',
        'evaluation_participant_id',
        'nama_peserta',
        'jabatan_lokasi_kerja',
        'responses',
        'mengetahui_atasan_nama',
        'mengetahui_atasan_tanggal',
        'mengetahui_personalia_nama',
        'mengetahui_personalia_tanggal'
    ];

    protected $casts = [
        'responses' => 'array',
        'mengetahui_atasan_tanggal' => 'date',
        'mengetahui_personalia_tanggal' => 'date',
    ];

    public function evaluationProgram()
    {
        return $this->belongsTo(EvaluationProgram::class);
    }

    public function participant()
    {
        return $this->belongsTo(EvaluationParticipant::class, 'evaluation_participant_id');
    }
}