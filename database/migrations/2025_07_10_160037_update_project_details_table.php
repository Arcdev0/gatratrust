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
        Schema::table('project_details', function (Blueprint $table) {
            // Ubah nama kolom
            $table->renameColumn('start', 'start_plan');
            $table->renameColumn('end', 'end_plan');

            // Tambah kolom baru
            $table->dateTime('start_action')->nullable()->after('end_plan');
            $table->dateTime('end_action')->nullable()->after('start_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_details', function (Blueprint $table) {
            // Kembalikan nama kolom
            $table->renameColumn('start_plan', 'start');
            $table->renameColumn('end_plan', 'end');

            // Hapus kolom baru
            $table->dropColumn(['start_action', 'end_action']);
        });
    }
};
