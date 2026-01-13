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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // PIC
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Opsional: siapa yang membuat task (kalau task dibuat oleh admin untuk user lain)
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // jenis task: project / umum
            $table->enum('jenis', ['project', 'umum'])->default('umum');

            // Kalau jenis=project, isi ini
            $table->foreignId('project_id')
                ->nullable()
                ->constrained('projects')
                ->nullOnDelete();

            // Ini dibiarkan nullable (nama tabel master kerjaan/proses kamu belum pasti)
            $table->unsignedBigInteger('kerjaan_id')->nullable();
            $table->unsignedBigInteger('proses_id')->nullable();

            // Kalau jenis=umum, pakai ini
            $table->string('judul_umum')->nullable();

            // detail awal / catatan umum task
            $table->text('deskripsi')->nullable();

            // status task master
            $table->enum('status', ['open', 'done'])->default('open');

            $table->date('started_at')->nullable();
            $table->date('finished_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status'], 'tasks_user_status_index');
            $table->index(['jenis'], 'tasks_jenis_index');
            $table->index(['project_id'], 'tasks_project_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
