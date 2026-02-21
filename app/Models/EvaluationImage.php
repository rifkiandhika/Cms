<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_program_id',
        'image_path',
        'caption',
        'order'
    ];

    public function evaluationProgram()
    {
        return $this->belongsTo(EvaluationProgram::class);
    }
}