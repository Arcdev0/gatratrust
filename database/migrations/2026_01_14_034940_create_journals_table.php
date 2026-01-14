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
        Schema::create('journals', function (Blueprint $table) {
            $table->id();

            $table->string('journal_no')->unique(); // contoh: JR-2026-000001
            $table->date('journal_date');

            // general | cash_in | cash_out
            $table->enum('type', ['general', 'cash_in', 'cash_out'])->default('general');

            // jenis transaksi (buat filter laporan)
            $table->string('category')->nullable(); // contoh: "operational_expense", "service_revenue", dll

            $table->string('reference_no')->nullable(); // nomor invoice / fpu / dll
            $table->string('source_module')->nullable(); // contoh: "invoice", "fpu", "manual"
            $table->unsignedBigInteger('source_id')->nullable(); // id dari module sumber (opsional)

            $table->text('memo')->nullable();

            // draft | posted | void
            $table->enum('status', ['draft', 'posted', 'void'])->default('draft');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('posted_by')->nullable();
            $table->timestamp('posted_at')->nullable();

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            // Index biar cepat
            $table->index(['journal_date', 'type']);
            $table->index(['source_module', 'source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
