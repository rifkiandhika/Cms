<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'program_number',
        'effective_date',
        'revision',
        'status',
        'description'
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    public function mainCategories()
    {
        return $this->hasMany(TrainingMainCategory::class)->orderBy('order');
    }

    // Helper untuk badge status
    public function getStatusBadgeAttribute()
    {
        return [
            'draft' => '<span class="badge bg-warning">Draft</span>',
            'active' => '<span class="badge bg-success">Aktif</span>',
            'archived' => '<span class="badge bg-secondary">Arsip</span>',
        ][$this->status] ?? '';
    }
}