<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_sub_category_id',
        'number',
        'nama_pelatihan',
        'peserta',
        'instruktur',
        'metode',
        'jadwal',
        'metode_penilaian',
        'order'
    ];

    public function subCategory()
    {
        return $this->belongsTo(TrainingSubCategory::class, 'training_sub_category_id');
    }

    public function details()
    {
        return $this->hasMany(TrainingDetail::class)->orderBy('order');
    }

    public function images()
    {
        return $this->hasMany(TrainingImage::class)->orderBy('order');
    }

    public function metadata()
    {
        return $this->hasOne(TrainingMetadata::class);
    }
}