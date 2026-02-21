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
        Schema::create('attendance_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_form_id')
                  ->constrained('attendance_forms')
                  ->onDelete('cascade');

            $table->string('nama_karyawan', 255);
            $table->string('jabatan', 255)->nullable();
            $table->string('lokasi_kerja', 255)->nullable();
            $table->string('paraf', 255)->nullable();
            $table->integer('urutan');
            
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendace_participants');
    }
};
