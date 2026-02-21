<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_program_id',
        'nama_peserta',
        'jabatan_lokasi_kerja',
        'order'
    ];

    public function evaluationProgram()
    {
        return $this->belongsTo(EvaluationProgram::class);
    }
}