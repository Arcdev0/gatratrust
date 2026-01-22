<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('accounting_settings', 'default_ap_coa_id')) {
                $table->unsignedBigInteger('default_ap_coa_id')->nullable()->after('default_ar_coa_id');
                $table->foreign('default_ap_coa_id')
                    ->references('id')->on('coa')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounting_settings', function (Blueprint $table) {
            if (Schema::hasColumn('accounting_settings', 'default_ap_coa_id')) {
                $table->dropForeign(['default_ap_coa_id']);
                $table->dropColumn('default_ap_coa_id');
            }
        });
    }
};
