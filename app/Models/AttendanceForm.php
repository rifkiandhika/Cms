<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceForm extends Model
{
    use HasFactory;

    protected $table = 'attendance_forms';
    protected $fillable = [
        'topik_pelatihan',
        'tanggal',
        'tempat',
        'instruktur',
        'catatan',
        'custom_columns',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'custom_columns' => 'array',
    ];

    public function participants()
    {
        return $this->hasMany(AttendanceParticipant::class)->orderBy('urutan');
    }
    public function getCustomColumnLabels(): array
    {
        return $this->custom_columns ?? [];
    }
}
