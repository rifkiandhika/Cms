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
    public function produks()
    {
        return $this->hasManyThrough(
            Produk::class,
            DetailSupplier::class,
            'supplier_id',  // Foreign key on detail_suppliers table
            'id',           // Foreign key on produks table
            'id',           // Local key on suppliers table
            'product_id'    // Local key on detail_suppliers table
        );
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'id_supplier', 'id_supplier');
    }
}
