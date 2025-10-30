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
         Schema::create('karyawan_pak', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pak_id')->constrained('paks')->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained('karyawan_data')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['pak_id', 'karyawan_id']); // biar tidak duplikat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_pak');
    }
};
