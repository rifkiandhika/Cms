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
        Schema::create('produk_satuans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('produk_id')
                ->constrained('produks')
                ->cascadeOnDelete();
            $table->string('kode_barcode')->nullable();
            $table->foreignUuid('satuan_id')
                ->constrained('satuans');
            $table->bigInteger('konversi')->default(1);
            // Berapa PCS dalam 1 unit ini. Satuan dasar (PCS) = konversi 1
            $table->boolean('is_default')->default(false);
            // true = satuan dasar (PCS)
            $table->timestamps();

            $table->unique(['produk_id', 'satuan_id']);
            $table->index('produk_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_satuans');
    }
};
