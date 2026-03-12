<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasUuids;

    protected $table = 'suppliers';
    protected $guarded = [];

    public function detailSuppliers()
    {
        return $this->hasMany(DetailSupplier::class);
    }

    // ← PERBAIKAN: FK lama 'product_id' → sekarang 'produk_id'
    public function produks()
    {
        return $this->hasManyThrough(
            Produk::class,
            DetailSupplier::class,
            'supplier_id', // FK di detail_suppliers
            'id',          // FK di produks
            'id',          // Local key di suppliers
            'produk_id'    // ← PERBAIKAN: dulu 'product_id'
        );
    }

    // ← PERBAIKAN: FK kedua salah ('id_supplier','id_supplier') → ('id_supplier','id')
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'id_supplier', 'id');
    }

    // ← TAMBAH: supplier_id sekarang ada di detail_gudangs
    public function detailGudangs()
    {
        return $this->hasMany(DetailGudang::class, 'supplier_id');
    }

    public function tagihans()
    {
        return $this->hasMany(TagihanPo::class, 'id_relasi', 'id')
            ->where('tipe_relasi', 'supplier');
    }
}
