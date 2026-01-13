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
        Schema::create('new_dailies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // 1 user hanya boleh 1 daily per tanggal
            $table->date('tanggal');

            $table->text('problem')->nullable();
            $table->text('summary')->nullable();
            $table->string('upload_file')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'tanggal'], 'dailies_user_tanggal_unique');
            $table->index(['tanggal'], 'dailies_tanggal_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_dailies');
    }
};
