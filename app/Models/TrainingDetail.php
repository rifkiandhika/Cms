<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_item_id',
        'letter',
        'content',
        'order'
    ];

    public function trainingItem()
    {
        return $this->belongsTo(TrainingItem::class, 'training_item_id');
    }
}