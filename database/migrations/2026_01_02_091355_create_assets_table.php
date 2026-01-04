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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->string('no_asset', 50)->unique();
            $table->string('nama', 150);
            $table->string('merek', 100)->nullable();
            $table->string('no_seri', 100)->nullable();

            $table->string('lokasi', 150);

            $table->unsignedInteger('jumlah')->default(1);

            // uang: pakai decimal biar aman (hindari float)
            $table->decimal('harga', 18, 2)->default(0);
            $table->decimal('total', 18, 2)->default(0);

            // url gambar (bisa null kalau belum ada)
            $table->string('url_gambar', 500)->nullable();

            // kode barcode yang ditempel di kursi
            $table->string('kode_barcode', 100)->unique();

            $table->timestamps();

            // optional: kalau sering search by lokasi
            $table->index('lokasi');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
