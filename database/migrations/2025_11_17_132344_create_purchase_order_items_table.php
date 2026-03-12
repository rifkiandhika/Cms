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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->uuid('id_po_item')->primary();
            $table->uuid('id_po');
            $table->uuid('id_produk'); // referensi ke tabel produk/obat
            $table->string('kode_produk')->nullable();
            $table->string('nama_produk', 200);
            $table->string('kondisi_barang')->nullable();

            $table->foreignUuid('produk_satuan_id');

            $table->bigInteger('konversi_snapshot')->default(1);
            
            // Qty dalam satuan transaksi (misal: 2 BOX)
            $table->integer('qty_diminta');
            $table->integer('qty_disetujui')->nullable();
            $table->integer('qty_diterima')->default(0);

            // Qty hasil konversi ke satuan dasar (misal: 2 BOX x 50 = 100 PCS)
            // Kolom inilah yang dipakai untuk update stok gudang
            $table->bigInteger('qty_diminta_satuan_dasar')->default(0);
            $table->bigInteger('qty_disetujui_satuan_dasar')->nullable();
            $table->bigInteger('qty_diterima_satuan_dasar')->default(0);
            
            // Harga per satuan transaksi (bukan per PCS)
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            
            $table->date('tanggal_kadaluarsa')->nullable(); // untuk tracking expiry
            $table->string('jenis', 50)->nullable();
            $table->string('batch_number', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_po')->references('id_po')->on('purchase_orders')->onDelete('cascade');

            $table->index('id_po');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
