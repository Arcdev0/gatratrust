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
        Schema::create('fpu_lines', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('fpu_id');
            $table->unsignedInteger('line_no')->default(1);

            $table->text('description');
            $table->decimal('amount', 18, 2)->default(0);

            // untuk validasi "paid" cepat (optional tapi recommended)
            $table->unsignedInteger('proof_count')->default(0);
            $table->boolean('has_proof')->default(false);

            $table->timestamps();

            $table->foreign('fpu_id')->references('id')->on('fpus')->cascadeOnDelete();

            $table->index(['fpu_id', 'line_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fpu_lines');
    }
};
