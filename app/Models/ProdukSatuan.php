<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProdukSatuan extends Model
{
    use HasUuids;

    protected $table = 'produk_satuans';

    protected $guarded = [];

    protected $casts = [
        'harga_otomatis' => 'boolean',
        'is_default'     => 'boolean',
        'isi'            => 'decimal:4',
        'harga_beli'     => 'decimal:2',
        'harga_jual'     => 'decimal:2',
    ];

    // Relasi
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    // Accessor: harga jual final (otomatis atau manual)
    public function getHargaJualFinalAttribute(): float
    {
        if ($this->harga_otomatis && $this->produk) {
            return (float) $this->produk->harga_dasar * (float) $this->isi;
        }
        return (float) $this->harga_jual;
    }

    public function getHargaBeliFinalAttribute(): float
    {
        if ($this->harga_otomatis && $this->produk) {
            return (float) $this->produk->harga_beli * (float) $this->isi;
        }
        return (float) $this->harga_beli;
    }

    // Hitung subtotal untuk qty tertentu
    public function hitungSubtotal(float $qty): array
    {
        return [
            'harga_jual'       => $this->harga_jual_final,
            'subtotal'         => $this->harga_jual_final * $qty,
            'qty_satuan_dasar' => $qty * (float) $this->isi,
        ];
    }
}