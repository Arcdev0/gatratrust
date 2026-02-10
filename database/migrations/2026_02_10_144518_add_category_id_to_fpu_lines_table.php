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
        Schema::table('fpu_lines', function (Blueprint $table) {
            // category_id mengacu ke tabel categories
            $table->unsignedBigInteger('category_id')
                ->nullable()
                ->after('fpu_id');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->nullOnDelete(); // kalau kategori dihapus, line tetap aman
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fpu_lines', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
