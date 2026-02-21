<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditResponse extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'evidence_date' => 'date',
        'temperature' => 'decimal:2',
    ];

    /**
     * Relasi ke Audit
     */
    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    /**
     * Relasi ke Question
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}