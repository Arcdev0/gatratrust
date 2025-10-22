<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mpi_tests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama_pt');
            $table->date('tanggal_running');
            $table->date('tanggal_inspection');
            $table->decimal('person', 15);
            $table->unsignedBigInteger('created_by');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mpi_test');
    }
};
