<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesertaJadwal extends Model
{
    use HasFactory;

    protected $table = 'peserta_jadwal';

    protected $fillable = [
        'jadwal_karyawan_id',
        'nama_karyawan',
        'catatan',
        'nilai',
        'status_kehadiran',
        'bukti'
    ];

    /**
     * Relasi ke jadwal karyawan
     */
    public function jadwal()
    {
        return $this->belongsTo(JadwalKaryawan::class, 'jadwal_karyawan_id');
    }

    /**
     * Accessor untuk warna badge status kehadiran
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'hadir' => 'success',
            'tidak_hadir' => 'danger',
            'izin' => 'warning',
            'sakit' => 'info',
        ];

        return $badges[$this->status_kehadiran] ?? 'secondary';
    }

    /**
     * Accessor untuk warna nilai (merah, kuning, hijau)
     */
    public function getNilaiBadgeAttribute()
    {
        if (!$this->nilai) return 'secondary';
        
        if ($this->nilai >= 80) return 'success';
        if ($this->nilai >= 60) return 'warning';
        return 'danger';
    }
}