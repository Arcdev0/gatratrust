<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['no_ref', 'invoice_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('no_ref')->nullable()->after('invoice_no');
            $table->enum('invoice_type', ['dp', 'pelunasan'])->default('dp')->after('no_ref');
        });
    }
};
