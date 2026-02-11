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
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('wallet_coa_id')
                ->after('amount_paid')
                ->nullable();

            // FK ke tabel coa
            $table->foreign('wallet_coa_id')
                ->references('id')
                ->on('coa')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->dropForeign(['wallet_coa_id']);
            $table->dropColumn('wallet_coa_id');
        });
    }
};
