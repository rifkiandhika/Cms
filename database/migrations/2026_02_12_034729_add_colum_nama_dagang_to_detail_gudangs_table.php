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
        Schema::table('detail_gudangs', function (Blueprint $table) {
            $table->string('kode_produk')->nullable()->after('barang_type');
            $table->string('nama_dagangan')->nullable()->after('kode_produk');
            $table->string('nomor_izin_edar')->nullable()->after('nama_dagangan');
            $table->string('tipe')->nullable()->after('nomor_izin_edar');
            $table->string('ukuran')->nullable()->after('tipe');
            $table->string('kemasan')->nullable()->after('ukuran');
            $table->string('satuan')->nullable()->after('kemasan');
            $table->string('satuan_lain')->nullable()->after('satuan');
            $table->string('kode_barcode')->nullable()->after('satuan_lain');
            $table->date('tanggal_produksi')->nullable()->after('tanggal_masuk');
            $table->integer('jumlah_keluar')->default(0)->after('stock_gudang');
            $table->integer('jumlah_retur')->default(0)->after('jumlah_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_gudangs', function (Blueprint $table) {
            //
        });
    }
};
