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
        Schema::create('catatan_suhu_ruangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kontrol_gudang_id')
                  ->constrained('kontrol_gudangs')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->date('tanggal')->comment('Tanggal pencatatan harian');
            $table->boolean('kebersihan')->default(false)->comment('Checklist kebersihan');
            $table->decimal('suhu_refrigerator', 5, 2)->nullable()->comment('Suhu refrigerator dalam °C');
            $table->decimal('suhu_ruangan', 5, 2)->nullable()->comment('Suhu ruangan dalam °C');
            $table->decimal('kelembapan', 5, 2)->nullable()->comment('Kelembapan dalam persen (%)');
            $table->boolean('keamanan')->default(false)->comment('Checklist keamanan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catatan_suhu_ruangans');
    }
};
