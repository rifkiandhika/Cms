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
        Schema::table('jadwal_karyawan', function (Blueprint $table) {
            $table->foreignId('sop_id')->nullable()->constrained('sops')->nullOnDelete()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_karyawan', function (Blueprint $table) {
            $table->dropForeign(['sop_id']);
            $table->dropColumn('sop_id');
        });
    }
};
