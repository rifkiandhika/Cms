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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->uuid('id_po')->primary();
            $table->string('no_po', 50)->unique();
            $table->string('no_gr', 255)->nullable(); // Good Receipt (auto saat diterima)

            $table->enum('tipe_po', ['penjualan', 'pembelian']);
            $table->enum('status', [
                'draft',
                'menunggu_persetujuan_kepala_gudang',
                'menunggu_persetujuan_kasir',
                'disetujui',
                'dikirim_ke_supplier',
                'dalam_pengiriman',
                'diterima',
                'ditolak',
                'dibatalkan'
            ])->default('draft');

            // =========================
            // Pemohon
            // =========================
            $table->uuid('id_unit_pemohon');
            $table->string('unit_pemohon');
            $table->uuid('id_karyawan_pemohon');
            $table->date('tanggal_permintaan');
            $table->text('catatan_pemohon')->nullable();

            // =========================
            // Tujuan
            // =========================
            $table->uuid('id_unit_tujuan')->nullable();
            $table->string('unit_tujuan')->nullable();

            // =========================
            // Approval Kepala Gudang
            // =========================
            $table->uuid('id_kepala_gudang_approval')->nullable();
            $table->timestamp('tanggal_approval_kepala_gudang')->nullable();
            $table->text('catatan_kepala_gudang')->nullable();
            $table->enum('status_approval_kepala_gudang', ['pending', 'disetujui', 'ditolak'])->default('pending');

            // =========================
            // Approval Kasir
            // =========================
            $table->uuid('id_kasir_approval')->nullable();
            $table->timestamp('tanggal_approval_kasir')->nullable();
            $table->enum('status_approval_kasir', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_kasir')->nullable();

            // =========================
            // Penerimaan Barang
            // =========================
            $table->uuid('id_penerima')->nullable();
            $table->dateTime('tanggal_diterima')->nullable();
            $table->text('catatan_penerima')->nullable();

            // =========================
            // Supplier
            // =========================
            $table->uuid('id_supplier')->nullable();
            $table->timestamp('tanggal_dikirim_ke_supplier')->nullable();
            $table->uuid('id_karyawan_pengirim')->nullable();

            // =========================
            // Invoice & Dokumen
            // =========================
            $table->string('no_invoice', 255)->nullable();
            $table->date('tanggal_invoice')->nullable();
            $table->string('surat_jalan', 255)->nullable();
            $table->string('bukti_invoice', 255)->nullable();
            $table->string('bukti_barang', 255)->nullable();
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->string('nomor_faktur_pajak', 255)->nullable();
            $table->string('no_kwitansi', 255)->nullable();

            $table->uuid('id_karyawan_input_invoice')->nullable();
            $table->timestamp('tanggal_input_invoice')->nullable();

            // =========================
            // Total
            // =========================
            $table->decimal('total_harga', 15, 2)->default(0);
            $table->decimal('pajak', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->decimal('total_diterima', 15, 2)->default(0);
            $table->decimal('pajak_diterima', 15, 2)->default(0);
            $table->decimal('grand_total_diterima', 15, 2)->default(0);

            // =========================
            // Pembatalan
            // =========================
            $table->string('catatan_pembatalan', 255)->nullable();
            $table->timestamp('tanggal_dibatalkan')->nullable();

            // =========================
            // Upload Bukti
            // =========================
            $table->timestamp('tanggal_upload_bukti')->nullable();
            $table->timestamp('tanggal_upload_bukti_barang')->nullable();
            $table->uuid('id_karyawan_upload_bukti')->nullable();
            $table->uuid('id_karyawan_upload_bukti_barang')->nullable();

            // =========================
            // Surat
            // =========================
            $table->date('tanggal_surat_jalan')->nullable();
            $table->string('no_surat', 255)->nullable();
            $table->date('tanggal_surat')->nullable();

            // =========================
            // Timestamp
            // =========================
            $table->timestamps();
            $table->softDeletes();

            // =========================
            // Foreign Key
            // =========================
            $table->foreign('id_karyawan_pemohon')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_kepala_gudang_approval')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_kasir_approval')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_pengirim')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_input_invoice')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_upload_bukti')->references('id_karyawan')->on('karyawans');
            $table->foreign('id_karyawan_upload_bukti_barang')->references('id_karyawan')->on('karyawans');

            $table->index(['no_po', 'status', 'tipe_po']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
