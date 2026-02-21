<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'sop_id',
        'section_code',
        'section_title',
        'order',
    ];

    /**
     * Relasi ke SOP
     */
    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }

    /**
     * Relasi ke items
     */
    public function items()
    {
        return $this->hasMany(SopSectionItem::class)->orderBy('order');
    }

    /**
     * Boot method untuk auto-delete cascade
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($section) {
            $section->items()->delete();
        });
    }
}