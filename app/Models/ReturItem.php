<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturItem extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'retur_items';
    protected $primaryKey = 'id_retur_item';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'qty_diretur'                      => 'integer',
        'qty_diterima_kembali'             => 'integer',
        'konversi_snapshot'                => 'integer',
        // ← TAMBAH: kolom baru dari perbaikan schema
        'qty_diretur_satuan_dasar'         => 'integer',
        'qty_diterima_kembali_satuan_dasar'=> 'integer',
        'tanggal_kadaluarsa'               => 'date',
        'harga_satuan'                     => 'decimal:2',
        'subtotal_retur'                   => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Hitung subtotal berdasarkan qty satuan (bukan satuan dasar)
            $model->subtotal_retur = $model->qty_diretur * $model->harga_satuan;

            // ← TAMBAH: auto-hitung qty satuan dasar saat save
            if ($model->konversi_snapshot) {
                $model->qty_diretur_satuan_dasar =
                    $model->qty_diretur * $model->konversi_snapshot;
                $model->qty_diterima_kembali_satuan_dasar =
                    $model->qty_diterima_kembali * $model->konversi_snapshot;
            }
        });

        static::saved(function ($model) {
            $model->retur->updateTotal();
        });

        static::deleted(function ($model) {
            $model->retur->updateTotal();
        });
    }

    public function retur(): BelongsTo
    {
        return $this->belongsTo(Retur::class, 'id_retur', 'id_retur');
    }

    // ← PERBAIKAN: dulu FK ke DetailobatRs (tidak ada di schema)
    // Seharusnya FK ke Produk
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id');
    }

    // ← TAMBAH: relasi ke satuan yang dipakai saat retur
    public function produkSatuan(): BelongsTo
    {
        return $this->belongsTo(ProdukSatuan::class, 'produk_satuan_id');
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'id_item_sumber', 'id_po_item');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ReturItemBatch::class, 'id_retur_item', 'id_retur_item');
    }

    public function getSisaQtyAttribute(): int
    {
        return $this->qty_diretur - $this->qty_diterima_kembali;
    }

    // ← TAMBAH: sisa dalam satuan dasar (PCS)
    public function getSisaQtySatuanDasarAttribute(): int
    {
        return $this->qty_diretur_satuan_dasar - $this->qty_diterima_kembali_satuan_dasar;
    }

    public function isComplete(): bool
    {
        return $this->qty_diterima_kembali >= $this->qty_diretur;
    }
}