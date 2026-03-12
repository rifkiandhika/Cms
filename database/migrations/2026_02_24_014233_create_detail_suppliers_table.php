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
        Schema::create('detail_suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete();
            $table->foreignUuid('produk_id') // ← RENAME dari product_id agar konsisten
                ->constrained('produks')
                ->cascadeOnDelete();
            $table->foreignUuid('produk_satuan_id')
                ->nullable()
                ->constrained('produk_satuans')
                ->nullOnDelete()
                ->comment('Satuan yang dipakai supplier saat jual ke kita (misal: BOX)');

            $table->decimal('harga_beli', 15, 2)->default(0)
                ->comment('Harga beli per produk_satuan_id di atas');
            $table->boolean('is_aktif')->default(true)
                ->comment('Apakah harga ini masih berlaku');
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Satu supplier hanya boleh punya 1 entri per produk per satuan
            $table->unique(['supplier_id', 'produk_id', 'produk_satuan_id'], 'uq_supplier_produk_satuan');
            $table->index('supplier_id');
            $table->index('produk_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_suppliers');
    }
};
