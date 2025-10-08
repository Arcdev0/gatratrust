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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // user yang melakukan aksi
            $table->string('reference', 100)->nullable();   // no invoice / no kwitansi
            $table->text('description');                    // deskripsi aksi
            $table->timestamp('created_at')->useCurrent();  // waktu aksi dicatat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
