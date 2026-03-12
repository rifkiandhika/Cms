<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PembayaranTagihan extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'pembayaran_tagihan';
    protected $primaryKey = 'id_pembayaran';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'tanggal_bayar'   => 'date',
        'tanggal_approve' => 'datetime',
        'jumlah_bayar'    => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_pembayaran)) {
                $model->id_pembayaran = (string) Str::uuid();
            }
            if (empty($model->no_pembayaran)) {
                $model->no_pembayaran = self::generateNoPembayaran();
            }
        });

        // Update tagihan hanya setelah pembayaran diverifikasi,
        // bukan langsung saat created. Panggil $tagihan->updatePembayaran()
        // di Service/Controller setelah status berubah menjadi 'diverifikasi'.
    }

    public static function generateNoPembayaran(): string
    {
        $prefix = 'PAY-' . date('Ymd') . '-';
        $last   = self::where('no_pembayaran', 'like', $prefix . '%')->orderBy('no_pembayaran', 'desc')->first();
        $newNumber = $last ? (int) substr($last->no_pembayaran, -3) + 1 : 1;
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // ── Relasi ──────────────────────────────────────────

    public function tagihan()
    {
        return $this->belongsTo(TagihanPo::class, 'id_tagihan', 'id_tagihan');
    }

    public function karyawanInput()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_input', 'id_karyawan');
    }

    public function karyawanApprove()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan_approve', 'id_karyawan');
    }

    // ── Accessors ─────────────────────────────────────────

    public function getFormattedJumlahBayarAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }

    // ── Helpers ───────────────────────────────────────────

    public function isPending(): bool { return $this->status_pembayaran === 'pending'; }
    public function isVerified(): bool { return $this->status_pembayaran === 'diverifikasi'; }

    // ── Scopes ──────────────────────────────────────────

    public function scopePending($query) { return $query->where('status_pembayaran', 'pending'); }
    public function scopeVerified($query) { return $query->where('status_pembayaran', 'diverifikasi'); }
    public function scopeToday($query) { return $query->whereDate('tanggal_bayar', today()); }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('tanggal_bayar', now()->year)
            ->whereMonth('tanggal_bayar', now()->month);
    }
}
