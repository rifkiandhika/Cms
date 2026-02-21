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
        // Parent/Wrapper untuk semua training categories
        Schema::create('training_programs', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Judul Program
            $table->string('program_number')->unique(); // No. Program (e.g., 01.PROTAP.CDAKB)
            $table->date('effective_date')->nullable(); // Tanggal Efektif
            $table->string('revision')->default('Rev. 00'); // Revisi
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // LEVEL 1: Main Categories (I, II, III - Roman Numerals)
        Schema::create('training_main_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_program_id')
                  ->constrained('training_programs')
                  ->onDelete('cascade');
            $table->string('roman_number'); // I, II, III, IV
            $table->string('name'); // PELATIHAN UMUM, PELATIHAN KHUSUS
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // LEVEL 2: Sub Categories (A, B, C - Letters)
        Schema::create('training_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_main_category_id')
                  ->constrained('training_main_categories')
                  ->onDelete('cascade');
            $table->string('letter'); // A, B, C, D
            $table->string('name'); // ORIENTASI UMUM, PELATIHAN CDAKB
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // LEVEL 3: Training Items (1, 2, 3 - Numbers)
        Schema::create('training_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_sub_category_id')
                  ->constrained('training_sub_categories')
                  ->onDelete('cascade');
            $table->string('number'); // 1, 2, 3, 4
            $table->text('nama_pelatihan'); // Pengenalan Perusahaan
            $table->text('peserta')->nullable();
            $table->text('instruktur')->nullable();
            $table->text('metode')->nullable();
            $table->text('jadwal')->nullable();
            $table->text('metode_penilaian')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // LEVEL 4: Details (a, b, c - Small Letters)
        Schema::create('training_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_item_id')
                  ->constrained('training_items')
                  ->onDelete('cascade');
            $table->string('letter'); // a, b, c, d
            $table->text('content'); // Sejarah Perusahaan
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Images
        Schema::create('training_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_item_id')
                  ->constrained('training_items')
                  ->onDelete('cascade');
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Metadata
        Schema::create('training_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_item_id')
                  ->constrained('training_items')
                  ->onDelete('cascade');
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('lokasi')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_metadata');
        Schema::dropIfExists('training_images');
        Schema::dropIfExists('training_details');
        Schema::dropIfExists('training_items');
        Schema::dropIfExists('training_sub_categories');
        Schema::dropIfExists('training_main_categories');
    }
};
