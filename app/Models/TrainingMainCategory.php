<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingMainCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_program_id', // Tambahkan ini
        'roman_number',
        'name',
        'order'
    ];

    public function trainingProgram()
    {
        return $this->belongsTo(TrainingProgram::class);
    }

    public function subCategories()
    {
        return $this->hasMany(TrainingSubCategory::class)->orderBy('order');
    }

    public function getFullNameAttribute()
    {
        return $this->roman_number . '. ' . $this->name;
    }
}