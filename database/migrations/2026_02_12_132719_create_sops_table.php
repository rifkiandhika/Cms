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
        Schema::create('sops', function (Blueprint $table) {
            $table->id();

            // Informasi utama SOP
            $table->string('nama_sop');
            $table->string('no_sop')->unique();
            $table->date('tanggal_dibuat');
            $table->date('tanggal_efektif');
            $table->string('revisi')->default('00');

            // Header & Branding
            $table->string('logo_path')->nullable(); 
            $table->string('judul_header')
                  ->default('PROSEDUR TETAP (PROTAP) PELATIHAN KARYAWAN');

            // Status dokumen
            $table->enum('status', ['draft', 'active', 'archived'])
                  ->default('draft');

            $table->timestamps();
            $table->softDeletes();
        });


        /**
         * 2. Tabel Section SOP (A. Tujuan, B. Ruang Lingkup, dst)
         */
        Schema::create('sop_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sop_id')
                  ->constrained('sops')
                  ->onDelete('cascade');

            $table->string('section_code');   // A, B, C, dst
            $table->string('section_title');  // Tujuan, Ruang Lingkup, dll
            $table->integer('order')->default(0);

            $table->timestamps();

            $table->index(['sop_id', 'order']);
        });


        /**
         * 3. Tabel Item dalam Section (mendukung nested)
         */
        Schema::create('sop_section_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sop_section_id')
                  ->constrained('sop_sections')
                  ->onDelete('cascade');

            $table->text('content'); // Isi point
            $table->integer('order')->default(0);

            // Level nested (1 = utama, 2 = sub-item, dst)
            $table->integer('level')->default(1);

            // Relasi parent untuk nested item
            $table->foreignId('parent_item_id')
                  ->nullable()
                  ->constrained('sop_section_items')
                  ->onDelete('cascade');

            $table->timestamps();

            $table->index(['sop_section_id', 'order']);
        });


        /**
         * 4. Tabel Approval / Signature
         */
        Schema::create('sop_approvals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sop_id')
                  ->constrained('sops')
                  ->onDelete('cascade');

            $table->string('keterangan'); // Dibuat Oleh, Disetujui Oleh, dll
            $table->string('nama')->nullable();
            $table->string('jabatan')->nullable();
            $table->date('tanda_tangan')->nullable();

            // Path tanda tangan digital
            $table->string('signature_path')->nullable();

            $table->integer('order')->default(0);

            $table->timestamps();

            $table->index(['sop_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sops');
    }
};
