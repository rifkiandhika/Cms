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
        // Menyimpan definisi kolom custom: [{"label": "Divisi"}, {"label": "No. HP"}]
        Schema::table('attendance_forms', function (Blueprint $table) {
            $table->json('custom_columns')->nullable()->after('catatan');
        });

        // Tambah custom_values di attendance_participants
        // Menyimpan nilai kolom custom per peserta: ["IT", "081234"]
        Schema::table('attendance_participants', function (Blueprint $table) {
            $table->json('custom_values')->nullable()->after('paraf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendace', function (Blueprint $table) {
            //
        });
    }
};
