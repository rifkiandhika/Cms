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
        Schema::create('purchase_order_item_batches', function (Blueprint $table) {
            $table->char('id', 36)->primary();

            $table->char('id_po_item', 36);
            $table->string('batch_number');
            $table->date('tanggal_kadaluarsa');
            $table->integer('qty_diterima');
            $table->enum('kondisi', ['baik', 'rusak', 'kadaluarsa']);
            $table->text('catatan')->nullable();

            $table->timestamps();

            $table->foreign('id_po_item')
                ->references('id_po_item')
                ->on('purchase_order_items')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_item_batches');
    }
};
