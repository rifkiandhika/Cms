<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DetailSupplier extends Model
{
    use HasUuids;

    protected $table = 'detail_suppliers';
    protected $guarded = [];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'is_aktif'   => 'boolean',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // ← HAPUS: detailGudangs() — DetailGudang sekarang berelasi langsung ke Supplier,
    //   bukan ke DetailSupplier. Tidak ada FK dari detail_gudangs ke detail_suppliers.

    // ← PERBAIKAN: FK lama 'product_id' → sekarang 'produk_id'
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function produkSatuan()
    {
        return $this->belongsTo(ProdukSatuan::class, 'produk_satuan_id');
    }
}
