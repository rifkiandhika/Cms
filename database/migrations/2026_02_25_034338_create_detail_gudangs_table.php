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
        Schema::create('detail_gudangs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('gudang_id')
                ->constrained('gudangs')
                ->onDelete('cascade');

            // ← GANTI: polymorphic dihapus, FK langsung ke produks
            $table->foreignUuid('produk_id')
                ->constrained('produks')
                ->onDelete('restrict');

            // ← PINDAHAN dari gudangs: supplier_id sekarang di level stok
            $table->foreignUuid('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->onDelete('set null')
                ->comment('Supplier asal barang ini. Nullable jika asal supplier tidak diketahui');

            $table->bigInteger('stock_gudang')->default(0)
                ->comment('Stok dalam satuan dasar (PCS)');
            $table->bigInteger('min_persediaan')->default(0)
                ->comment('Batas minimum stok untuk notifikasi, dalam PCS');

            $table->string('no_batch', 50)->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_produksi')->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->string('lokasi_rak', 50)->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak', 'Kadaluarsa'])->default('Baik');
            $table->timestamps();

            // ← UPDATE: unique sekarang mencakup supplier_id
            // Produk sama dari supplier berbeda = baris berbeda ✓
            // Produk sama dari supplier sama tapi batch berbeda = baris berbeda ✓
            $table->unique(
                ['gudang_id', 'produk_id', 'supplier_id', 'no_batch'],
                'uq_stok_gudang_produk_supplier_batch'
            );
            $table->index('gudang_id');
            $table->index('produk_id');
            $table->index('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_gudangs');
    }
};
