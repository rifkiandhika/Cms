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
        Schema::create('history_gudangs', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('gudang_id')->nullable();
            $table->uuid('referensi_id')->nullable();
            $table->uuid('supplier_id')->nullable();
            $table->uuid('barang_id');
            $table->string('no_batch')->nullable();
            $table->string('no_referensi')->nullable();
            $table->string('referensi_type')->nullable();
            $table->string('keterangan')->nullable();
            $table->unsignedBigInteger('jumlah');
            $table->dateTime('waktu_proses');
            $table->enum('status', ['penerimaan', 'pengiriman']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_gudangs');
    }
};
