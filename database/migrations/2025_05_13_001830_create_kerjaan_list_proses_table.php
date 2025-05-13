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
        Schema::create('kerjaan_list_proses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kerjaan_id')->constrained('kerjaans')->onDelete('cascade');
            $table->foreignId('list_proses_id')->constrained('list_proses')->onDelete('cascade');
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerjaan_list_proses');
    }
};
