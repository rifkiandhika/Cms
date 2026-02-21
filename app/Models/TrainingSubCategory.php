<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_main_category_id',
        'letter',
        'name',
        'order'
    ];

    public function mainCategory()
    {
        return $this->belongsTo(TrainingMainCategory::class, 'training_main_category_id');
    }

    public function trainingItems()
    {
        return $this->hasMany(TrainingItem::class)->orderBy('order');
    }

    public function getFullNameAttribute()
    {
        return $this->letter . '. ' . $this->name;
    }
}