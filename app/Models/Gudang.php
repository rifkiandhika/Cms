<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasUuids;

    protected $table = 'gudangs';
    protected $guarded = [];


    public function details()
    {
        return $this->hasMany(DetailGudang::class);
    }

    // ← TAMBAH: akses stock movements melalui gudang
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'gudang_id');
    }

    // ── Scopes ──────────────────────────────────────────

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }
}
