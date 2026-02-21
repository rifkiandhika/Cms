<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DetailGudang extends Model
{
    use HasUuids;
    protected $table = 'detail_gudangs';
    protected $guarded = [];

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'barang_id', 'id');
    }
    
    public function barangSupplier()
    {
        return $this->belongsTo(DetailSupplier::class, 'barang_id', 'detail_obat_rs_id');
    }
}
