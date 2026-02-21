<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopSectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sop_section_id',
        'content',
        'order',
        'level',
        'parent_item_id',
    ];

    /**
     * Relasi ke section
     */
    public function section()
    {
        return $this->belongsTo(SopSection::class, 'sop_section_id');
    }

    /**
     * Relasi ke parent item (untuk nested items)
     */
    public function parent()
    {
        return $this->belongsTo(SopSectionItem::class, 'parent_item_id');
    }

    /**
     * Relasi ke child items
     */
    public function children()
    {
        return $this->hasMany(SopSectionItem::class, 'parent_item_id')->orderBy('order');
    }
}