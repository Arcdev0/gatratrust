<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {

            // Drop FK dulu (pakai array col untuk resolve constraint name)
            if (Schema::hasColumn('accounting_settings', 'default_cash_coa_id')) {
                $table->dropForeign(['default_cash_coa_id']);
                $table->dropColumn('default_cash_coa_id');
            }

            if (Schema::hasColumn('accounting_settings', 'default_bank_coa_id')) {
                $table->dropForeign(['default_bank_coa_id']);
                $table->dropColumn('default_bank_coa_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('default_cash_coa_id')->nullable()->after('id');
            $table->unsignedBigInteger('default_bank_coa_id')->nullable()->after('default_cash_coa_id');

            $table->foreign('default_cash_coa_id')->references('id')->on('coa')->nullOnDelete();
            $table->foreign('default_bank_coa_id')->references('id')->on('coa')->nullOnDelete();
        });
    }
};
