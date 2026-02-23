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
        Schema::create('detail_customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id');
            $table->uuid('product_id')->nullable();
            $table->string('no_batch')->nullable();
            $table->string('judul')->nullable();
            $table->string('nama', 200);
            $table->string('jenis', 100);
            $table->string('merk', 100)->nullable();
            $table->string('satuan', 50);
            $table->date('exp_date')->nullable();
            $table->integer('stock_live')->default(0);
            $table->integer('stock_po')->default(0);
            $table->integer('min_persediaan')->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->string('kode_rak', 50)->nullable();
            $table->timestamps();

            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('cascade');

            $table->foreign('product_id')
                  ->references('id')
                  ->on('produks')
                  ->onDelete('set null');

            $table->index('customer_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_customers');
    }
};
