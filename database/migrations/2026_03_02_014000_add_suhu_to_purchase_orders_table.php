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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('suhu_barang_dikirim', 5, 2)->nullable()->after('grand_total')
                  ->comment('Suhu barang saat dikirim (°C)');
            $table->decimal('suhu_barang_datang', 5, 2)->nullable()->after('suhu_barang_dikirim')
                  ->comment('Suhu barang saat datang / diterima (°C)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['suhu_barang_dikirim', 'suhu_barang_datang']);
        });
    }
};
