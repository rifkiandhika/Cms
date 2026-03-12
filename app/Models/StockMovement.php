<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasUuids;

    protected $table = 'stock_movements';
    protected $guarded = [];

    protected $casts = [
        'qty_sebelum'       => 'integer',
        'qty_perubahan'     => 'integer',
        'qty_sesudah'       => 'integer',
        'tanggal_kadaluarsa' => 'date',
    ];

    // ── Relasi ──────────────────────────────────────────

    public function gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    // ── Accessor ─────────────────────────────────────────

    /**
     * Apakah ini mutasi masuk?
     */
    public function getIsMasukAttribute(): bool
    {
        return $this->qty_perubahan > 0;
    }

    /**
     * Apakah ini mutasi keluar?
     */
    public function getIsKeluarAttribute(): bool
    {
        return $this->qty_perubahan < 0;
    }

    /**
     * Nilai absolut perubahan (selalu positif)
     */
    public function getQtyAbsoluteAttribute(): int
    {
        return abs($this->qty_perubahan);
    }

    // ── Scopes ──────────────────────────────────────────

    public function scopeMasuk($query)
    {
        return $query->where('qty_perubahan', '>', 0);
    }

    public function scopeKeluar($query)
    {
        return $query->where('qty_perubahan', '<', 0);
    }

    public function scopeByTipe($query, string $tipe)
    {
        return $query->where('tipe', $tipe);
    }

    public function scopePeriode($query, $dari, $sampai)
    {
        return $query->whereBetween('created_at', [$dari, $sampai]);
    }

    public function scopeByGudangProduk($query, string $gudangId, string $produkId)
    {
        return $query->where('gudang_id', $gudangId)->where('produk_id', $produkId);
    }
}
