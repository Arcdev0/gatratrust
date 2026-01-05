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
            $table->string('faktur_pembelian')->nullable()->after('url_gambar');
            $table->year('tahun_dibeli')->nullable()->after('faktur_pembelian');
            $table->enum('remark', ['baik', 'perlu_perbaikan', 'rusak', 'hilang'])->nullable()->after('tahun_dibeli');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'faktur_pembelian',
                'tahun_dibeli',
                'remark',
            ]);
        });
    }
};
