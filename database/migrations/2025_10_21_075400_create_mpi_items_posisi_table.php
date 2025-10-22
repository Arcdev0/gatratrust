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
        Schema::create('mpi_items_posisi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mpi_item_id');
            $table->enum('nama_posisi', [
                '1G','2G','3G','4G','5G','6G',
                '1F','2F','3F','4F'
            ])->nullable();
            $table->foreign('mpi_item_id')->references('id')->on('mpi_items')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpi_items_posisi');
    }
};
