<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sop extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'tanggal_dibuat' => 'date',
        'tanggal_efektif' => 'date',
    ];

    /**
     * Relasi ke sections
     */
    public function sections()
    {
        return $this->hasMany(SopSection::class)->orderBy('order');
    }

    /**
     * Relasi ke approvals
     */
    public function approvals()
    {
        return $this->hasMany(SopApproval::class)->orderBy('order');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
}