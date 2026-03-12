<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TagihanPo extends Model
{
    use HasUuids;

    protected $table = 'tagihan_po';
    protected $primaryKey = 'id_tagihan';
    protected $guarded = [];

    protected $casts = [
        'tanggal_tagihan'   => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'tanggal_dibuat'    => 'datetime',
        'tanggal_approve'   => 'datetime',
        'total_tagihan'     => 'decimal:2',
        'pajak'             => 'decimal:2',
        'grand_total'       => 'decimal:2',
        'total_dibayar'     => 'decimal:2',
        'tenor_hari'        => 'integer',
        // ← HAPUS: 'sisa_tagihan' => 'decimal:2'
        // Kolom ini sudah dihapus dari schema, gunakan accessor di bawah
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id_tagihan)) {
                $model->id_tagihan = (string) Str::uuid();
            }
            if (empty($model->no_tagihan)) {
                $model->no_tagihan = self::generateNoTagihan();
            }
            if (empty($model->tanggal_dibuat)) {
                $model->tanggal_dibuat = now();
            }
        });
    }

    public static function generateNoTagihan()
    {
        $prefix = 'TAG-' . date('Ymd') . '-';
        $last   = self::where('no_tagihan', 'like', $prefix . '%')->orderBy('no_tagihan', 'desc')->first();
        $newNumber = $last ? (int) substr($last->no_tagihan, -3) + 1 : 1;
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // ── Relasi ──────────────────────────────────────────

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'id_po', 'id_po');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_relasi', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'id_relasi', 'id');
    }

    public function relasi()
    {
        if ($this->tipe_relasi === 'customer') {
            return $this->belongsTo(Customer::class, 'id_relasi', 'id');
        }
        return $this->belongsTo(Supplier::class, 'id_relasi', 'id');
    }

    public function items()
    {
        return $this->hasMany(TagihanPoItem::class, 'id_tagihan', 'id_tagihan');
    }

    public function pembayaran()
    {
        return $this->hasMany(PembayaranTagihan::class, 'id_tagihan', 'id_tagihan');
    }

    public function karyawanBuat()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_buat', 'id_karyawan');
    }

    public function karyawanApprove()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_approve', 'id_karyawan');
    }

    // ── Accessor ─────────────────────────────────────────

    /**
     * ← PERBAIKAN: sisa_tagihan dihitung dari grand_total - total_dibayar
     * Tidak perlu kolom di database, cukup accessor ini
     */
    public function getSisaTagihanAttribute(): float
    {
        return (float) $this->grand_total - (float) $this->total_dibayar;
    }

    public function getNamaRelasiAttribute()
    {
        if ($this->tipe_relasi === 'customer') {
            return $this->customer->nama_customer ?? '-';
        }
        return $this->supplier->nama_supplier ?? '-';
    }

    public function getPersenTerbayarAttribute(): float
    {
        if ($this->grand_total == 0) return 0;
        return round(($this->total_dibayar / $this->grand_total) * 100, 2);
    }

    public function getIsJatuhTempoAttribute(): bool
    {
        if (!$this->tanggal_jatuh_tempo) return false;
        return now()->isAfter($this->tanggal_jatuh_tempo) && !$this->isLunas();
    }

    public function getPaymentPercentageAttribute(): float
    {
        if ($this->grand_total <= 0) return 0;
        return ($this->total_dibayar / $this->grand_total) * 100;
    }

    // ── Helpers ───────────────────────────────────────────

    public function isLunas(): bool { return $this->status === 'lunas'; }
    public function isDraft(): bool { return $this->status === 'draft'; }

    public function isOverdue(): bool
    {
        if (!$this->tanggal_jatuh_tempo) return false;
        return now()->isAfter($this->tanggal_jatuh_tempo)
            && !in_array($this->status, ['lunas', 'dibatalkan']);
    }

    public function isDueSoon(): bool
    {
        if (!$this->tanggal_jatuh_tempo) return false;
        $daysLeft = now()->diffInDays($this->tanggal_jatuh_tempo, false);
        return $daysLeft > 0 && $daysLeft <= 7;
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, ['menunggu_pembayaran', 'dibayar_sebagian'])
            // ← PERBAIKAN: pakai accessor sisa_tagihan, bukan kolom
            && $this->sisa_tagihan > 0;
    }

    /**
     * ← PERBAIKAN: updatePembayaran tidak lagi update kolom sisa_tagihan
     * karena kolom itu sudah dihapus dari schema
     */
    public function updatePembayaran(): void
    {
        $totalDibayar = $this->pembayaran()
            ->where('status_pembayaran', 'diverifikasi')
            ->sum('jumlah_bayar');

        $this->total_dibayar = $totalDibayar;
        // sisa_tagihan = grand_total - total_dibayar (via accessor, tidak perlu disimpan)

        $sisaTagihan = $this->grand_total - $totalDibayar;

        if ($sisaTagihan <= 0) {
            $this->status = 'lunas';
        } elseif ($totalDibayar > 0) {
            $this->status = 'dibayar_sebagian';
        }

        $this->save();
    }

    // ── Scopes ──────────────────────────────────────────

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['menunggu_pembayaran', 'dibayar_sebagian']);
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['menunggu_pembayaran', 'dibayar_sebagian'])
            ->where('tanggal_jatuh_tempo', '<', now());
    }

    public function scopeDueWithinDays($query, $days = 7)
    {
        return $query->whereIn('status', ['menunggu_pembayaran', 'dibayar_sebagian'])
            ->whereBetween('tanggal_jatuh_tempo', [now(), now()->addDays($days)]);
    }

    public function scopeNeedAttention($query)
    {
        return $query->whereIn('status', ['menunggu_pembayaran', 'dibayar_sebagian'])
            ->where(function ($q) {
                $q->where('tanggal_jatuh_tempo', '<', now())
                    ->orWhereBetween('tanggal_jatuh_tempo', [now(), now()->addDays(7)]);
            });
    }
}
