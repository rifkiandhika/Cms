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

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            
            // Auto-generate kode_produk jika kosong
            if (empty($model->kode_produk)) {
                $model->kode_produk = self::generateKodeProduk();
            }
        });
    }

    /**
     * Generate kode produk otomatis
     */
    public static function generateKodeProduk()
    {
        $prefix = 'PRD';
        $date = date('Ymd');
        
        // Cari kode terakhir hari ini
        $lastProduk = self::where('kode_produk', 'like', $prefix . $date . '%')
            ->orderBy('kode_produk', 'desc')
            ->first();
        
        if ($lastProduk) {
            // Ambil 4 digit terakhir dan tambahkan 1
            $lastNumber = (int) substr($lastProduk->kode_produk, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke tabel Jenis
     */
    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'jenis', 'nama_jenis');
    }

    public function satuanDasar()
    {
        return $this->belongsTo(Satuan::class, 'satuan_dasar_id');
    }

    public function produkSatuans()
    {
        return $this->hasMany(ProdukSatuan::class);
    }

    public function satuanDefault()
    {
        return $this->hasOne(ProdukSatuan::class)->where('is_default', true);
    }

    /**
     * Scope untuk filter status aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk filter status nonaktif
     */
    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    
}