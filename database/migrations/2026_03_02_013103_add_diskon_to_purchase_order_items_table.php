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
        Schema::table('purchase_order_items', function (Blueprint $table) {
            // Persentase diskon per item (0-100)
            $table->decimal('diskon_persen', 8, 2)->default(0)->after('harga_satuan')
                  ->comment('Diskon dalam persen (0-100)');

            // Nominal diskon per satuan (harga_satuan * diskon_persen / 100)
            $table->decimal('diskon_nominal', 15, 2)->default(0)->after('diskon_persen')
                  ->comment('Nominal diskon per satuan');

            // Harga setelah diskon per satuan
            $table->decimal('harga_setelah_diskon', 15, 2)->default(0)->after('diskon_nominal')
                  ->comment('Harga per satuan setelah diskon');

            // Flag item gratis (diskon 100%)
            $table->boolean('is_free')->default(false)->after('harga_setelah_diskon')
                  ->comment('True jika item ini diberikan gratis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn([
                'diskon_persen',
                'diskon_nominal',
                'harga_setelah_diskon',
                'is_free',
            ]);
        });
    }
};
