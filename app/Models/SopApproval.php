<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'sop_id',
        'keterangan',
        'nama',
        'jabatan',
        'tanda_tangan',
        'signature_path',
        'order',
    ];

    protected $casts = [
        'tanda_tangan' => 'date',
    ];

    /**
     * Relasi ke SOP
     */
    public function sop()
    {
        return $this->belongsTo(Sop::class);
    }
}