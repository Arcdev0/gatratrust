<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fpu_line_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('fpu_line_id');

            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            $table->unsignedBigInteger('uploaded_by')->nullable();

            $table->timestamps();

            $table->foreign('fpu_line_id')->references('id')->on('fpu_lines')->cascadeOnDelete();
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['fpu_line_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fpu_line_attachments');
    }
};
