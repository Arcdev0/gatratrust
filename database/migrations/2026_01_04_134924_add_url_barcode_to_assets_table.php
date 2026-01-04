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
         Schema::table('assets', function (Blueprint $table) {
            $table->string('url_barcode', 500)->nullable()->after('url_gambar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('url_barcode');
        });
    }
};
