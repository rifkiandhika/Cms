<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'name',
        'order'
    ];

    public function subCategories()
    {
        return $this->hasMany(TrainingSubCategory::class)->orderBy('order');
    }
}