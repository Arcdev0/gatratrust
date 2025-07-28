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
        Schema::table('accountings', function (Blueprint $table) {
            $table->dateTime('tanggal')->after('no_jurnal');
            $table->decimal('debit', 15, 2)->after('total')->default(0);
            $table->decimal('credit', 15, 2)->after('debit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accountings', function (Blueprint $table) {
            $table->dropColumn(['tanggal', 'debit', 'credit']);
        });
    }
};
