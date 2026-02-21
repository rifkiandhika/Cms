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
        Schema::create('kontrol_gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('periode', 20)->comment('Contoh: September 2023');
            $table->string('nama_gudang')->comment('Nama Ruang/Refrigerator');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrol_gudangs');
    }
};
