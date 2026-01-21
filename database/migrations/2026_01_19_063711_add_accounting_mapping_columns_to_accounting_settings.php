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
        Schema::table('accounting_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('default_ar_coa_id')->nullable()->after('default_bank_coa_id');
            $table->unsignedBigInteger('default_sales_coa_id')->nullable()->after('default_ar_coa_id');
            $table->unsignedBigInteger('default_tax_payable_coa_id')->nullable()->after('default_sales_coa_id');
            $table->unsignedBigInteger('default_expense_coa_id')->nullable()->after('default_tax_payable_coa_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {
            $table->dropColumn([
                'default_ar_coa_id',
                'default_sales_coa_id',
                'default_tax_payable_coa_id',
                'default_expense_coa_id',
            ]);
        });
    }
};
