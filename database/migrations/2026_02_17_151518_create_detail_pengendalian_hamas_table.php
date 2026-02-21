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
        Schema::create('detail_pengendalian_hamas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengendalian_hama_id')
                  ->constrained('pengendalian_hamas')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            // Waktu
            $table->date('tanggal');
            $table->string('hari', 15)->comment('e.g. Sabtu');
            $table->time('waktu')->nullable();

            // Treatment (checklist)
            $table->boolean('treatment_c')->default(false)->comment('CoolFog');
            $table->boolean('treatment_b')->default(false)->comment('Baiting');
            $table->boolean('treatment_f')->default(false)->comment('Fogging');
            $table->boolean('treatment_i')->default(false)->comment('Inspeksi');

            // Perangkap
            $table->string('perangkap_perlakuan', 10)->nullable()->comment('e.g. RB, K, MS, L');
            $table->integer('jumlah_hama')->default(0);

            // Evaluasi & Petugas
            $table->string('evaluasi')->nullable();
            $table->string('nama_petugas')->nullable();
            $table->boolean('paraf_petugas')->default(false);

            // Keterangan
            $table->string('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pengendalian_hama_gambar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengendalian_hama_id')
                  ->constrained('pengendalian_hamas')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->string('path_gambar');
            $table->string('nama_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengendalian_hama_gambar');
        Schema::dropIfExists('detail_pengendalian_hamas');
    }
};
