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
        // Program Evaluasi (Header)
        Schema::create('evaluation_programs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('materi_pelatihan');
            $table->date('hari_tanggal')->nullable();
            $table->string('tempat_pelatihan')->nullable();
            $table->string('program_number')->unique();
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Item Evaluasi (Langsung tanpa kategori nested)
        // A, B, C, D adalah item evaluasi
        Schema::create('evaluation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_program_id')
                  ->constrained('evaluation_programs')
                  ->onDelete('cascade');
            $table->string('item_label'); // A, B, C, D
            $table->text('item_content'); // Pertanyaan/Konten
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Peserta
        Schema::create('evaluation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_program_id')
                  ->constrained('evaluation_programs')
                  ->onDelete('cascade');
            $table->string('nama_peserta');
            $table->string('jabatan_lokasi_kerja')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Responses dari Peserta
        Schema::create('evaluation_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_program_id')
                  ->constrained('evaluation_programs')
                  ->onDelete('cascade');
            $table->foreignId('evaluation_participant_id')
                  ->nullable()
                  ->constrained('evaluation_participants')
                  ->onDelete('cascade');
            $table->string('nama_peserta');
            $table->string('jabatan_lokasi_kerja')->nullable();
            $table->json('responses'); // JSON: {item_id: jawaban}
            
            // Yang Mengetahui - Atasan
            $table->string('mengetahui_atasan_nama')->nullable();
            $table->date('mengetahui_atasan_tanggal')->nullable();
            
            // Yang Mengetahui - Bagian Personalia
            $table->string('mengetahui_personalia_nama')->nullable();
            $table->date('mengetahui_personalia_tanggal')->nullable();
            
            $table->timestamps();
        });

        // Images
        Schema::create('evaluation_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_program_id')
                  ->constrained('evaluation_programs')
                  ->onDelete('cascade');
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_images');
        Schema::dropIfExists('evaluation_responses');
        Schema::dropIfExists('evaluation_participants');
        Schema::dropIfExists('evaluation_items');
        Schema::dropIfExists('evaluation_programs');
    }
};
