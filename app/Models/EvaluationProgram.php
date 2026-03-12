<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'sop_id',
        'title',
        'materi_pelatihan',
        'hari_tanggal',
        'tempat_pelatihan',
        'program_number',
        'status',
        'description'
    ];

    protected $casts = [
        'hari_tanggal' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(EvaluationItem::class)->orderBy('order');
    }

    public function participants()
    {
        return $this->hasMany(EvaluationParticipant::class)->orderBy('order');
    }

    public function responses()
    {
        return $this->hasMany(EvaluationResponse::class);
    }

    public function images()
    {
        return $this->hasMany(EvaluationImage::class)->orderBy('order');
    }
}