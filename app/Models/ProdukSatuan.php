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
        'is_default' => 'boolean',
        'konversi'   => 'integer',
        // ← HAPUS: harga_otomatis, isi, harga_beli, harga_jual
        // Kolom-kolom itu tidak ada di schema produk_satuans.
        // harga_beli ada di detail_suppliers, harga_jual di detail_customers
    ];

    // ── Relasi ──────────────────────────────────────────

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function detailSuppliers()
    {
        return $this->hasMany(DetailSupplier::class);
    }

    public function detailCustomers()
    {
        return $this->hasMany(DetailCustomer::class);
    }

    // ── Scope ────────────────────────────────────────────

    /**
     * Lookup satuan berdasarkan scan barcode.
     * Contoh: ProdukSatuan::findByBarcode('8991234567890')
     *         → return ProdukSatuan (BOX, konversi 50) beserta produknya
     *
     * Usage: ProdukSatuan::with('produk')->findByBarcode($kodeBarcode)
     */
    public function scopeFindByBarcode($query, string $barcode)
    {
        return $query->where('kode_barcode', $barcode);
    }

    // ── Accessor ─────────────────────────────────────────

    // ← HAPUS: getHargaJualFinalAttribute & getHargaBeliFinalAttribute
    // Accessor ini bergantung pada kolom harga_otomatis, harga_dasar, isi
    // yang tidak ada di schema. Harga diambil dari detail_suppliers/detail_customers.

    /**
     * Hitung qty dalam satuan dasar (PCS) untuk qty satuan ini.
     * Contoh: qty=2 BOX, konversi=50 → hasil 100 PCS
     */
    public function hitungQtySatuanDasar(float $qty): int
    {
        return (int) ($qty * $this->konversi);
    }

    /**
     * Hitung subtotal untuk qty tertentu berdasarkan harga yang diberikan.
     * Harga harus diambil dari detail_suppliers atau detail_customers.
     */
    public function hitungSubtotal(float $qty, float $hargaPerSatuan): array
    {
        return [
            'subtotal'         => $hargaPerSatuan * $qty,
            'qty_satuan_dasar' => $this->hitungQtySatuanDasar($qty),
        ];
    }

    public function getLabelAttribute(): string
    {
        $namaSatuan = $this->satuan?->nama_satuan ?? '-';
        $konversi   = $this->konversi ?? 1;
        return "{$namaSatuan} ({$konversi} satuan dasar)";
    }
}