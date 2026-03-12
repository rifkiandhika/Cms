<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailCustomer extends Model
{
    use HasUuids;

    protected $table = 'detail_customers';
    protected $guarded = [];

    protected $casts = [
        'harga_jual' => 'decimal:2',
        'is_aktif'   => 'boolean',
        // ← HAPUS: exp_date, stock_live, stock_po, min_persediaan
        // Kolom-kolom ini sudah dihapus dari schema detail_customers
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // ← PERBAIKAN: FK lama 'product_id' → sekarang 'produk_id'
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function produkSatuan(): BelongsTo
    {
        return $this->belongsTo(ProdukSatuan::class, 'produk_satuan_id');
    }
}