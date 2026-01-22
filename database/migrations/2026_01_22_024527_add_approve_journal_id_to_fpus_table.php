<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fpus', function (Blueprint $table) {
            if (!Schema::hasColumn('fpus', 'approve_journal_id')) {
                $table->unsignedBigInteger('approve_journal_id')->nullable()->after('wallet_coa_id');
                $table->foreign('approve_journal_id')
                    ->references('id')->on('journals')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('fpus', function (Blueprint $table) {
            if (Schema::hasColumn('fpus', 'approve_journal_id')) {
                $table->dropForeign(['approve_journal_id']);
                $table->dropColumn('approve_journal_id');
            }
        });
    }
};
