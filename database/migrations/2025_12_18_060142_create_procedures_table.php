<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();

            $table->string('no_dok', 100)->unique();
            $table->string('nama_dok', 255);
            $table->date('tanggal_berlaku')->nullable();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            // optional pointer ke revisi terakhir/aktif (bukan status)
            // $table->foreignId('current_revision_id')->nullable()
            //     ->constrained('procedure_revisions')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
