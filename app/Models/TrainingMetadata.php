<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingMetadata extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_item_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'lokasi',
        'catatan'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function trainingItem()
    {
        return $this->belongsTo(TrainingItem::class, 'training_item_id');
    }
}