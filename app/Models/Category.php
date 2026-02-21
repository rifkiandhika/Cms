<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke SubCategory
     */
    public function subCategories()
    {
        return $this->hasMany(SubCategory::class)->orderBy('order');
    }

    /**
     * Relasi ke Questions melalui SubCategory
     */
    public function questions()
    {
        return $this->hasManyThrough(Question::class, SubCategory::class);
    }
}