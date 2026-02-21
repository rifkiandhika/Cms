<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_program_id',
        'item_label',
        'item_content',
        'order'
    ];

    public function evaluationProgram()
    {
        return $this->belongsTo(EvaluationProgram::class);
    }
}