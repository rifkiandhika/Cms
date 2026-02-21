<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceParticipant extends Model
{
    use HasFactory;

    protected $table = 'attendance_participants';
    protected $fillable = [
        'attendance_form_id',
        'nama_karyawan',
        'jabatan',
        'lokasi_kerja',
        'paraf',
        'urutan',
        'custom_values',
    ];

    protected $casts = [
        'custom_values' => 'array',
    ];

    public function attendanceForm()
    {
        return $this->belongsTo(AttendanceForm::class);
    }

    public function getCustomValue(int $index): string
    {
        return $this->custom_values[$index] ?? '';
    }
}