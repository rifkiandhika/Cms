<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DetailGudang extends Model
{
    use HasUuids;

    protected $table   = 'detail_gudangs';
    protected $guarded = [];

    protected $casts = [
        'stock_gudang'       => 'integer',
        'min_persediaan'     => 'integer',
        'tanggal_masuk'      => 'date',
        'tanggal_produksi'   => 'date',
        'tanggal_kadaluarsa' => 'date',
    ];

    // ── Relasi ──────────────────────────────────────────────────

    public function gudang()
    {
        return $this->belongsTo(Gudang::class);
    }

    public function produk()
    {
        // FK langsung ke produks (bukan barang_id polymorphic lagi)
        return $this->belongsTo(Produk::class, 'produk_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'gudang_id', 'gudang_id')
            ->where('produk_id', $this->produk_id);
    }

    // ── Accessor ────────────────────────────────────────────────

    /**
     * Tampilkan stok dalam format mudah dibaca: "4 BOX, 30 PCS"
     */
    public function getStokDalamSatuanAttribute(): string
    {
        $stok   = $this->stock_gudang ?? 0;
        $satuan = $this->produk?->produkSatuans
            ?->sortByDesc('konversi')
            ->filter(fn($s) => !$s->is_default);

        if (!$satuan || $satuan->isEmpty()) {
            return "{$stok} PCS";
        }

        $hasil = [];
        foreach ($satuan as $s) {
            $jumlah = (int) floor($stok / $s->konversi);
            if ($jumlah > 0) {
                $hasil[] = "{$jumlah} " . ($s->satuan->nama_satuan ?? 'unit');
                $stok   -= $jumlah * $s->konversi;
            }
        }
        if ($stok > 0) {
            $hasil[] = "{$stok} PCS";
        }

        return implode(', ', $hasil) ?: '0 PCS';
    }

    // ── Helper ──────────────────────────────────────────────────

    /**
     * Apakah stok di bawah minimum persediaan?
     */
    public function isBelowMinimum(): bool
    {
        return ($this->stock_gudang ?? 0) < ($this->min_persediaan ?? 0);
    }

    /**
     * Apakah produk sudah atau akan segera kadaluarsa?
     * Default: 30 hari ke depan.
     */
    public function isNearExpiry(int $days = 30): bool
    {
        if (!$this->tanggal_kadaluarsa) return false;
        return $this->tanggal_kadaluarsa->lte(now()->addDays($days));
    }

    /**
     * Total stok yang sudah keluar (dihitung dari stock_movements).
     * Tidak perlu kolom jumlah_keluar di tabel.
     *
     * @param string|null $dari   Format Y-m-d
     * @param string|null $sampai Format Y-m-d
     */
    public function jumlahKeluar(?string $dari = null, ?string $sampai = null): int
    {
        $query = StockMovement::where('gudang_id', $this->gudang_id)
            ->where('produk_id', $this->produk_id)
            ->where('qty_perubahan', '<', 0);

        if ($this->no_batch) {
            $query->where('no_batch', $this->no_batch);
        }
        if ($dari && $sampai) {
            $query->whereBetween('created_at', [$dari, $sampai]);
        }

        return (int) $query->sum(DB::raw('ABS(qty_perubahan)'));
    }

    /**
     * Total stok yang dikembalikan dari customer (dihitung dari stock_movements).
     *
     * @param string|null $dari   Format Y-m-d
     * @param string|null $sampai Format Y-m-d
     */
    public function jumlahRetur(?string $dari = null, ?string $sampai = null): int
    {
        $query = StockMovement::where('gudang_id', $this->gudang_id)
            ->where('produk_id', $this->produk_id)
            ->where('tipe', 'retur_dari_customer');

        if ($this->no_batch) {
            $query->where('no_batch', $this->no_batch);
        }
        if ($dari && $sampai) {
            $query->whereBetween('created_at', [$dari, $sampai]);
        }

        return (int) $query->sum('qty_perubahan');
    }
}