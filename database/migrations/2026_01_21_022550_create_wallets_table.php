<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('accounting_setting_id');
            $table->unsignedBigInteger('coa_id');

            $table->timestamps();

            $table->foreign('accounting_setting_id')
                ->references('id')->on('accounting_settings')
                ->cascadeOnDelete();

            $table->foreign('coa_id')
                ->references('id')->on('coa')
                ->cascadeOnDelete();

            // biar tidak dobel 1 setting + 1 coa
            $table->unique(['accounting_setting_id', 'coa_id']);

            $table->index('accounting_setting_id');
            $table->index('coa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
