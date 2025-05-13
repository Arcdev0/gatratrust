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
        Schema::create('project_progress_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_detail_id')->constrained('project_details')->onDelete('cascade');
            $table->foreignId('list_proses_file_id')->constrained('list_proses_files')->onDelete('cascade');
            $table->string('file_path');
            $table->string('keterangan')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_progress_files');
    }
};
