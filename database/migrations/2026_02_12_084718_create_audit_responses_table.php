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
        Schema::create('audit_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->enum('response', ['yes', 'no', 'na', 'partial'])->nullable()->comment('Ya, Tidak, N/A, Sebagian');
            $table->text('evidence')->nullable()->comment('bukti/catatan'); 
            $table->string('document_path')->nullable()->comment('path file dokumen jika upload');
            $table->string('image_path')->nullable()->comment('path gambar bukti');
            $table->date('evidence_date')->nullable()->comment('tanggal bukti/kejadian'); 
            $table->decimal('temperature', 5, 2)->nullable()->comment('suhu (misal: 25.50°C)');
            $table->text('notes')->nullable()->comment('catatan tambahan');
            $table->timestamps();
            
            $table->unique(['audit_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_responses');
    }
};
