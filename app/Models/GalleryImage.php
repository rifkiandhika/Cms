<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    use HasFactory;

    protected $fillable = ['gallery_id', 'image_path', 'image_name'];

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }

    public function getClientOriginalExtension()
    {
        return pathinfo($this->image_name, PATHINFO_EXTENSION);
    }
}