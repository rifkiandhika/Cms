<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke SubCategory
     */
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    /**
     * Relasi ke Category melalui SubCategory
     */
    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            SubCategory::class,
            'id', // Foreign key on sub_categories table
            'id', // Foreign key on categories table
            'sub_category_id', // Local key on questions table
            'category_id' // Local key on sub_categories table
        );
    }

    /**
     * Relasi ke AuditResponse
     */
    public function auditResponses()
    {
        return $this->hasMany(AuditResponse::class);
    }
}