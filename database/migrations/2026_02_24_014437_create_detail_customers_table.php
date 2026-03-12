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
       Schema::create('detail_customers', function (Blueprint $table) {
        $table->uuid('id')->primary();

        $table->foreignUuid('customer_id')
            ->constrained('customers')
            ->cascadeOnDelete();
        $table->foreignUuid('produk_id') // ← RENAME dari product_id agar konsisten
            ->nullable()
            ->constrained('produks')
            ->nullOnDelete();
        $table->foreignUuid('produk_satuan_id')
            ->nullable()
            ->constrained('produk_satuans')
            ->nullOnDelete()
            ->comment('Satuan yang dipakai customer saat beli dari kita (misal: PCS atau BOX)');

        $table->decimal('harga_jual', 15, 2)->default(0)
            ->comment('Harga jual per produk_satuan_id di atas');
        $table->boolean('is_aktif')->default(true)
            ->comment('Apakah harga ini masih berlaku');
        $table->text('catatan')->nullable();
        $table->timestamps();

        // Satu customer hanya boleh punya 1 entri per produk per satuan
        $table->unique(['customer_id', 'produk_id', 'produk_satuan_id'], 'uq_customer_produk_satuan');
        $table->index('customer_id');
        $table->index('produk_id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_customers');
    }
};
