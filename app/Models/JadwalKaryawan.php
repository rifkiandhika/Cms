<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalKaryawan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwal_karyawan';

    protected $guarded = [];

    protected $fillable = [
        'sop_id',
        'tanggal',
        'nama_acara',
        'deskripsi',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
    ];

    /**
     * Relasi ke peserta jadwal
     */
    public function peserta()
    {
        return $this->hasMany(PesertaJadwal::class);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('tanggal', $year)
                    ->whereMonth('tanggal', $month);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    /**
     * Get rata-rata nilai dari peserta
     */
    public function getRataNilaiAttribute()
    {
        return $this->peserta()->whereNotNull('nilai')->avg('nilai');
    }

    /**
     * Get total peserta
     */
    public function getTotalPesertaAttribute()
    {
        return $this->peserta()->count();
    }
}