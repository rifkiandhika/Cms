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
        'exp_date'    => 'date',
        'harga_jual'  => 'decimal:2',
        'stock_live'  => 'integer',
        'stock_po'    => 'integer',
        'min_persediaan' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'product_id');
    }
}