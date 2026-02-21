<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_item_id',
        'image_path',
        'caption',
        'order'
    ];

    public function trainingItem()
    {
        return $this->belongsTo(TrainingItem::class, 'training_item_id');
    }
}