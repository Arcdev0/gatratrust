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
         Schema::create('vendor', function (Blueprint $table) {
            $table->id();
            $table->string('nama_vendor', 100);
            $table->string('nama_perusahaan', 150)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nomor_telepon', 20);
            $table->string('email', 100)->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor');
    }
};
