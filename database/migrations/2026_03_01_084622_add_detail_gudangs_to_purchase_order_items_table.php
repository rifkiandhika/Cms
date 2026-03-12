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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->foreignUuid('detail_gudang_id')
            ->nullable()
            ->after('id_produk')
            ->constrained('detail_gudangs')
            ->onDelete('set null')
            ->comment('Referensi langsung ke batch stok gudang asal (untuk penjualan)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            //
        });
    }
};
