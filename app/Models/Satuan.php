<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Satuan extends Model
{
    use HasUuids;

    protected $table = 'satuans';
    protected $primaryKey = 'id';

    protected $guarded = [];

    // ========================
    // RELASI
    // ========================

    public function produk_satuans()
    {
        return $this->hasMany(ProdukSatuan::class, 'satuan_id');
    }

    public function produks()
    {
        return $this->belongsToMany(Produk::class, 'produk_satuans', 'satuan_id', 'produk_id')
            ->withPivot(['konversi', 'is_default'])
            ->withTimestamps();
    }
}