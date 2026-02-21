<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel utama untuk jadwal/acara
        Schema::create('jadwal_karyawan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('nama_acara');
            $table->text('deskripsi')->nullable();
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('tanggal');
        });

        // 2. Tabel untuk peserta jadwal (karyawan yang terlibat)
        Schema::create('peserta_jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_karyawan_id')->constrained('jadwal_karyawan')->onDelete('cascade');
            $table->string('nama_karyawan')->nullable();
            $table->text('catatan')->nullable();
            $table->integer('nilai')->nullable()->comment('Nilai kinerja 1-100');
            $table->enum('status_kehadiran', ['hadir', 'tidak_hadir', 'izin', 'sakit'])->default('hadir');
            $table->string('bukti')->nullable();
            $table->timestamps();
            
            $table->index(['jadwal_karyawan_id', 'nama_karyawan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_karyawans');
    }
};
