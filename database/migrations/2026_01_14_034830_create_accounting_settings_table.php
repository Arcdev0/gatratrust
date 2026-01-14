<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('accounting_settings', function (Blueprint $table) {
            $table->id();

            // Default akun penting
            $table->unsignedBigInteger('default_cash_coa_id')->nullable();     // Kas kecil / cash
            $table->unsignedBigInteger('default_bank_coa_id')->nullable();     // Bank utama
            $table->unsignedBigInteger('default_suspense_coa_id')->nullable(); // Akun penampung sementara
            $table->unsignedBigInteger('default_retained_earning_coa_id')->nullable();

            // Penomoran jurnal
            $table->string('journal_prefix')->default('JR');
            $table->unsignedInteger('journal_running_number')->default(1);

            // Setting periode (opsional, simple)
            $table->unsignedTinyInteger('fiscal_year_start_month')->default(1); // 1=Jan

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('default_cash_coa_id')->references('id')->on('coa')->nullOnDelete();
            $table->foreign('default_bank_coa_id')->references('id')->on('coa')->nullOnDelete();
            $table->foreign('default_suspense_coa_id')->references('id')->on('coa')->nullOnDelete();
            $table->foreign('default_retained_earning_coa_id')->references('id')->on('coa')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_settings');
    }
};
