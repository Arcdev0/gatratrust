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
         Schema::create('journal_lines', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('journal_id');
            $table->unsignedBigInteger('coa_id');

            $table->text('description')->nullable();

            // debit & credit (double-entry)
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('credit', 18, 2)->default(0);

            // Optional untuk kebutuhan Boss Gatra (nanti kepake buat laporan)
            $table->unsignedBigInteger('project_id')->nullable();  // kalau ada modul project
            $table->unsignedBigInteger('customer_id')->nullable(); // kalau ada modul client/customer
            $table->unsignedBigInteger('vendor_id')->nullable();   // kalau ada modul vendor/supplier

            // Kalau Boss punya dompet/cash/bank table, bisa connect di sini:
            $table->unsignedBigInteger('wallet_id')->nullable();

            $table->unsignedInteger('line_no')->default(1);

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');
            $table->foreign('coa_id')->references('id')->on('coa')->restrictOnDelete();

            $table->index(['journal_id', 'coa_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_lines');
    }
};
