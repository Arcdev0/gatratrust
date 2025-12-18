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
        Schema::table('procedures', function (Blueprint $table) {
            // 1) tambah kolom dulu
            $table->unsignedBigInteger('current_revision_id')->nullable()->after('created_by');

            // 2) baru tambahkan foreign key
            $table->foreign('current_revision_id')
                ->references('id')
                ->on('procedure_revisions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('procedures', function (Blueprint $table) {
            $table->dropForeign(['current_revision_id']);
            $table->dropColumn('current_revision_id');
        });
    }
};
