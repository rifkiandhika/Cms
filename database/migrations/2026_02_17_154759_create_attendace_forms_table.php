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
        Schema::create('attendance_forms', function (Blueprint $table) {
            $table->id();
            $table->string('topik_pelatihan', 255);
            $table->date('tanggal')->nullable();
            $table->string('tempat', 255)->nullable();
            $table->string('instruktur', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendace_forms');
    }
};
