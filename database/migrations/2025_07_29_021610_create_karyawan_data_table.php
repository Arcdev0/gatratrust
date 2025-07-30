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
        Schema::create('karyawan_data', function (Blueprint $table) {
            $table->id();
            $table->string('no_karyawan')->unique();
            $table->string('nama_lengkap');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat_lengkap');
            $table->foreignId('jabatan_id')->constrained('jabatan')->onDelete('cascade');
            $table->boolean('status')->default(1);
            $table->string('nomor_telepon', 20)->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('nomor_identitas', 50)->unique()->nullable();
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Duda', 'Janda'])->nullable();
            $table->enum('kewarganegaraan', ['WNI', 'WNA'])->default('WNI');
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'])->nullable();
            $table->string('pekerjaan')->nullable();
            $table->date('doh')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan_data');
    }
};
