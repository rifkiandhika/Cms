<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produks';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    // protected $casts = [
        
    // ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            if (empty($model->kode_produk)) {
                $model->kode_produk = self::generateKodeProduk();
            }
        });
    }

    public static function generateKodeProduk(): string
    {
        $prefix = 'PRD';
        $date   = date('Ymd');

        $lastProduk = self::where('kode_produk', 'like', $prefix . $date . '%')
            ->orderBy('kode_produk', 'desc')
            ->first();

        $newNumber = $lastProduk
            ? ((int) substr($lastProduk->kode_produk, -4)) + 1
            : 1;

        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // ── Relasi ──────────────────────────────────────────

    public function jenis()
    {
        // Relasi via string nama_jenis (tetap dipertahankan)
        return $this->belongsTo(Jenis::class, 'jenis', 'nama_jenis');
    }

    // ← HAPUS: satuanDasar() — kolom satuan_dasar_id tidak ada di schema

    public function produkSatuans()
    {
        return $this->hasMany(ProdukSatuan::class);
    }

    public function satuanDefault()
    {
        return $this->hasOne(ProdukSatuan::class)->where('is_default', true);
    }

    // ← TAMBAH: relasi yang sebelumnya tidak ada
    public function detailGudangs()
    {
        return $this->hasMany(DetailGudang::class, 'produk_id');
    }

    public function detailSuppliers()
    {
        return $this->hasMany(DetailSupplier::class, 'produk_id');
    }

    public function detailCustomers()
    {
        return $this->hasMany(DetailCustomer::class, 'produk_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'produk_id');
    }

    // ── Scopes ──────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    // ── Accessor ─────────────────────────────────────────

    /**
     * Total stok produk ini dari semua gudang (dalam PCS)
     */
    public function getTotalStokAttribute(): int
    {
        return $this->detailGudangs()->sum('stock_gudang');
    }
}