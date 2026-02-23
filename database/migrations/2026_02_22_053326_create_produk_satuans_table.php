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
            $table->foreignUuid('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->foreignUuid('satuan_id')->constrained('satuans');
            $table->string('label')->comment('misal: Galon, Box, PCS, Lusin');
            $table->bigInteger('isi')->comment('berapa satuan dasar dalam 1 unit ini');
            // Contoh: 1 Galon = 500 test → isi = 500
            //         1 Box   = 500 pcs → isi = 500
            //         1 PCS   = 1 pcs   → isi = 1
            $table->decimal('harga_beli', 15, 2)->comment('harga beli per unit satuan ini');
            $table->decimal('harga_jual', 15, 2)->comment('harga jual per unit satuan ini');
            // harga_jual bisa di-override dari harga_dasar × isi, atau custom
            $table->boolean('harga_otomatis')->default(true)->comment('hitung otomatis dari harga_dasar x isi');
            $table->boolean('is_default')->default(false)->comment('satuan default saat transaksi');
            $table->timestamps();
        });
        
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn('satuan');
            $table->foreignUuid('satuan_dasar_id')->nullable()->constrained('satuans');
            $table->decimal('harga_dasar', 15, 2)->default(0)->after('harga_jual');
            // harga_beli & harga_jual tetap sebagai harga default/referensi
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
