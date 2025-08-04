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
        Schema::create('timeline_tahunan', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');                 
            $table->date('start_date');            
            $table->date('end_date');              
            $table->string('description');        
            $table->boolean('is_action')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timeline_tahunan');
    }
};
