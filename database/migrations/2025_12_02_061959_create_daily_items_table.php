<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('daily_id');

            $table->enum('jenis', ['project', 'umum']);

            $table->unsignedBigInteger('project_id')->nullable(); 
            $table->unsignedBigInteger('kerjaan_id')->nullable();
            $table->unsignedBigInteger('proses_id')->nullable();
            $table->string('pekerjaan_umum')->nullable();
            $table->text('keterangan')->nullable();

            $table->boolean('status')->default(true)->comment('true = ok, false = belum');

            $table->timestamps();

            $table->foreign('daily_id')->references('id')->on('dailies')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            $table->foreign('kerjaan_id')->references('id')->on('kerjaans')->nullOnDelete();
            $table->foreign('proses_id')->references('id')->on('list_proses')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_items');
    }
};
