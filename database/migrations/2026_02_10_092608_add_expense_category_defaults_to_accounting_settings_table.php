<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('default_expense_honorarium_coa_id')->nullable()->after('default_expense_coa_id');
            $table->unsignedBigInteger('default_expense_operational_coa_id')->nullable()->after('default_expense_honorarium_coa_id');
            $table->unsignedBigInteger('default_expense_consumable_coa_id')->nullable()->after('default_expense_operational_coa_id');
            $table->unsignedBigInteger('default_expense_building_coa_id')->nullable()->after('default_expense_consumable_coa_id');
            $table->unsignedBigInteger('default_expense_other_coa_id')->nullable()->after('default_expense_building_coa_id');

            // optional FK (kalau tabel coa pakai id yang sama)
            $table->foreign('default_expense_honorarium_coa_id')->references('id')->on('coa')->nullOnDelete();
            $table->foreign('default_expense_operational_coa_id')->references('id')->on('coa')->nullOnDelete();
            $table->foreign('default_expense_consumable_coa_id')->references('id')->on('coa')->nullOnDelete();
            $table->foreign('default_expense_building_coa_id')->references('id')->on('coa')->nullOnDelete();
            $table->foreign('default_expense_other_coa_id')->references('id')->on('coa')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {
            $table->dropForeign(['default_expense_honorarium_coa_id']);
            $table->dropForeign(['default_expense_operational_coa_id']);
            $table->dropForeign(['default_expense_consumable_coa_id']);
            $table->dropForeign(['default_expense_building_coa_id']);
            $table->dropForeign(['default_expense_other_coa_id']);

            $table->dropColumn([
                'default_expense_honorarium_coa_id',
                'default_expense_operational_coa_id',
                'default_expense_consumable_coa_id',
                'default_expense_building_coa_id',
                'default_expense_other_coa_id',
            ]);
        });
    }
};
