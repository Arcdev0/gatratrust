<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paks', function (Blueprint $table) {
            $table->foreignId('customer_user_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->index('customer_user_id');
        });

        $quotationsWithoutPakCount = DB::table('quotation')->whereNull('pak_id')->count();
        if ($quotationsWithoutPakCount > 0) {
            throw new RuntimeException("Migration dibatalkan: masih ada {$quotationsWithoutPakCount} quotation tanpa pak_id. Silakan mapping data secara manual terlebih dahulu.");
        }

        Schema::table('quotation', function (Blueprint $table) {
            $table->dropForeign(['pak_id']);
            $table->foreignId('pak_id')->nullable(false)->change();
            $table->foreign('pak_id')->references('id')->on('paks')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->dropForeign(['pak_id']);
            $table->foreignId('pak_id')->nullable()->change();
            $table->foreign('pak_id')->references('id')->on('paks')->nullOnDelete();
        });

        Schema::table('paks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_user_id');
        });
    }
};
