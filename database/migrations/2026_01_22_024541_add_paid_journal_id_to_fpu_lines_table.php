<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('fpu_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('fpu_lines', 'paid_journal_id')) {
                $table->unsignedBigInteger('paid_journal_id')->nullable()->after('has_proof');
                $table->foreign('paid_journal_id')
                    ->references('id')->on('journals')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('fpu_lines', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid_journal_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fpu_lines', function (Blueprint $table) {
            if (Schema::hasColumn('fpu_lines', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
            if (Schema::hasColumn('fpu_lines', 'paid_journal_id')) {
                $table->dropForeign(['paid_journal_id']);
                $table->dropColumn('paid_journal_id');
            }
        });
    }
};
