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
        Schema::create('paks', function (Blueprint $table) {
            $table->id();
            $table->string('pak_name');
            $table->unsignedBigInteger('pak_value')->default(0);
            $table->string('pak_number')->unique();
            $table->enum('location', ['dalam_kota', 'luar_kota']);
            $table->date('date')->nullable();
            $table->unsignedBigInteger('po_amount')->nullable();
            $table->decimal('pph_23', 14, 2)->nullable();
            $table->decimal('ppn', 14, 2)->nullable();
            $table->unsignedBigInteger('total_pak_cost')->nullable();
            $table->bigInteger('estimated_profit')->nullable();
            $table->decimal('total_cost_percentage', 8, 2)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('pak_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paks');
    }
};
