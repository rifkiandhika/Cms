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
        Schema::create('pengendalian_hamas', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi');
            $table->string('bulan', 20)->comment('e.g. September');
            $table->year('tahun')->comment('e.g. 2023');
            $table->string('penanggung_jawab')->nullable()->comment('Nama penanggung jawab teknis');
            $table->string('paraf_pj')->nullable()->comment('Path file paraf/tanda tangan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengendalian_hamas');
    }
};
