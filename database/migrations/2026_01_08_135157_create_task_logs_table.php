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
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_id')
                ->nullable()
                ->constrained('new_dailies')
                ->nullOnDelete();

            $table->foreignId('task_id')
                ->constrained('tasks')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tanggal');
            $table->text('keterangan')->nullable();

            $table->enum('status_hari_ini', ['lanjut', 'done'])->default('lanjut');

            $table->string('upload_file')->nullable();

            $table->timestamps();
            $table->unique(['task_id', 'tanggal'], 'task_logs_task_tanggal_unique');

            $table->index(['tanggal'], 'task_logs_tanggal_index');
            $table->index(['daily_id'], 'task_logs_daily_index');
            $table->index(['user_id', 'tanggal'], 'task_logs_user_tanggal_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_logs');
    }
};
