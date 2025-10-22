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
        Schema::create('mpi_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mpi_test_id');

            // Data utama
            $table->string('nama_jurulas')->nullable();
            $table->string('foto_jurulas')->nullable();
            $table->string('foto_ktp')->nullable();

            // proses_las: enum (bisa pilih kombinasi)
            $table->enum('proses_las', ['SMAW', 'FCAW', 'SMAW & FCAW'])->nullable();

            // Foto dokumentasi
            $table->string('foto_sebelum')->nullable();
            $table->string('foto_during')->nullable();
            $table->string('foto_hasil')->nullable();

            // Foto MPI
            $table->string('foto_sebelum_mpi')->nullable();
            $table->string('foto_setelah_mpi')->nullable();
            $table->timestamps();

            $table->foreign('mpi_test_id')->references('id')->on('mpi_tests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpi_items');
    }
};
