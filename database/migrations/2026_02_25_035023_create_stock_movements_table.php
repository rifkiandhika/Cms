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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('gudang_id')
                ->constrained('gudangs')
                ->onDelete('restrict');
            $table->foreignUuid('produk_id')
                ->constrained('produks')
                ->onDelete('restrict');

            $table->enum('tipe', [
                'pembelian',           // Stok masuk dari PO pembelian
                'penjualan',           // Stok keluar karena PO penjualan
                'retur_dari_customer', // Stok masuk kembali karena customer retur
                'retur_ke_supplier',   // Stok keluar karena diretur ke supplier
                'penyesuaian_masuk',   // Koreksi manual tambah stok (stock opname)
                'penyesuaian_keluar',  // Koreksi manual kurang stok (stock opname)
                'transfer_masuk',      // Stok masuk dari gudang lain
                'transfer_keluar',     // Stok keluar ke gudang lain
                'kadaluarsa',          // Stok dihapus karena expired
                'rusak'                // Stok dihapus karena rusak
            ]);

            // Referensi ke transaksi sumber
            $table->string('referensi_tipe', 50)->nullable()
                ->comment('Nama tabel sumber: purchase_orders, returs, dll');
            $table->uuid('referensi_id')->nullable()
                ->comment('ID record di tabel referensi_tipe');
            $table->string('referensi_no', 100)->nullable()
                ->comment('Nomor dokumen: no_po, no_retur — untuk kemudahan baca manusia');

            // Nilai perubahan — selalu dalam satuan dasar (PCS)
            $table->bigInteger('qty_sebelum')
                ->comment('Stok sebelum perubahan, dalam PCS');
            $table->bigInteger('qty_perubahan')
                ->comment('Positif = masuk, Negatif = keluar, dalam PCS');
            $table->bigInteger('qty_sesudah')
                ->comment('Stok setelah perubahan, dalam PCS. Harus = qty_sebelum + qty_perubahan');

            // Info batch yang bergerak
            $table->string('no_batch', 50)->nullable();
            $table->date('tanggal_kadaluarsa')->nullable();

            $table->uuid('id_karyawan');
            $table->text('catatan')->nullable();

            $table->timestamps(); // created_at = waktu mutasi terjadi

            $table->foreign('id_karyawan')->references('id_karyawan')->on('karyawans');

            $table->index('gudang_id');
            $table->index('produk_id');
            $table->index(['gudang_id', 'produk_id']); // query stok per gudang per produk
            $table->index('tipe');
            $table->index(['referensi_tipe', 'referensi_id']); // balik-lookup dari PO ke movement
            $table->index('created_at'); // filter per periode
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
