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
        if (Schema::hasTable('training_programs') && !Schema::hasColumn('training_programs', 'sop_id')) {
            Schema::table('training_programs', function (Blueprint $table) {
                $table->foreignId('sop_id')->nullable()->constrained('sops')->nullOnDelete()->after('id');
            });
        }

        // Evaluation Programs
        if (Schema::hasTable('evaluation_programs') && !Schema::hasColumn('evaluation_programs', 'sop_id')) {
            Schema::table('evaluation_programs', function (Blueprint $table) {
                $table->foreignId('sop_id')->nullable()->constrained('sops')->nullOnDelete()->after('id');
            });
        }

        // Attendance Forms
        if (Schema::hasTable('attendance_forms') && !Schema::hasColumn('attendance_forms', 'sop_id')) {
            Schema::table('attendance_forms', function (Blueprint $table) {
                $table->foreignId('sop_id')->nullable()->constrained('sops')->nullOnDelete()->after('id');
            });
        }

        // Galleries
        if (Schema::hasTable('galleries') && !Schema::hasColumn('galleries', 'sop_id')) {
            Schema::table('galleries', function (Blueprint $table) {
                $table->foreignId('sop_id')->nullable()->constrained('sops')->nullOnDelete()->after('id');
            });
        }

        // Kontrol Gudang
        if (Schema::hasTable('kontrol_gudangs') && !Schema::hasColumn('kontrol_gudangs', 'sop_id')) {
            Schema::table('kontrol_gudangs', function (Blueprint $table) {
                $table->foreignId('sop_id')->nullable()->constrained('sops')->nullOnDelete()->after('id');
            });
        }

        // Pengendalian Hama
        if (Schema::hasTable('pengendalian_hamas') && !Schema::hasColumn('pengendalian_hamas', 'sop_id')) {
            Schema::table('pengendalian_hamas', function (Blueprint $table) {
                $table->foreignId('sop_id')->nullable()->constrained('sops')->nullOnDelete()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'training_programs',
            'evaluation_programs',
            'attendance_forms',
            'galleries',
            'kontrol_gudangs',
            'pengendalian_hamas',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'sop_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['sop_id']);
                    $t->dropColumn('sop_id');
                });
            }
        }
    }
};
