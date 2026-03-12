<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PurchaseOrderItem extends Model
{
    use HasUuids;

    protected $table = 'purchase_order_items';
    protected $primaryKey = 'id_po_item';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'tanggal_kadaluarsa'         => 'date',
        'konversi_snapshot'          => 'integer',
        'qty_diminta'                => 'integer',
        'qty_disetujui'              => 'integer',
        'qty_diterima'               => 'integer',
        'qty_diminta_satuan_dasar'   => 'integer',
        'qty_disetujui_satuan_dasar' => 'integer',
        'qty_diterima_satuan_dasar'  => 'integer',
        'harga_satuan'               => 'decimal:2',
        'subtotal'                   => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id_po_item = (string) Str::uuid();
        });
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_po', 'id_po');
    }

    // ← PERBAIKAN: dulu FK ke DetailSupplier (salah arah)
    // Seharusnya FK ke Produk karena id_produk adalah ID dari tabel produks
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id');
    }

    public function produkSatuan()
    {
        return $this->belongsTo(ProdukSatuan::class, 'produk_satuan_id');
    }

    public function batches()
    {
        return $this->hasMany(PurchaseOrderItemBatch::class, 'id_po_item', 'id_po_item');
    }

    public function detailGudang()
    {
        return $this->belongsTo(DetailGudang::class, 'detail_gudang_id');
    }

    public function getTotalQtyDiterimaFromBatches()
    {
        return $this->batches()->sum('qty_diterima');
    }

    public function getTotalQtyBaikFromBatches()
    {
        return $this->batches()->where('kondisi', 'baik')->sum('qty_diterima');
    }
}
