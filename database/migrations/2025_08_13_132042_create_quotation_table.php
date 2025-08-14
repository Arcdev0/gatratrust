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
        Schema::create('quotation', function (Blueprint $table) {
            $table->id();
            $table->string('quo_no')->unique();
            $table->date('date');
            $table->string('customer_name');
            $table->string('customer_address')->nullable();
            $table->string('attention')->nullable();
            $table->string('your_reference')->nullable();
            $table->string('terms')->nullable();
            $table->string('job_no')->nullable();
            $table->integer('rev')->default(0);
            $table->decimal('total_amount', 20, 2)->default(0);
            $table->decimal('discount', 20, 2)->default(0);
            $table->decimal('sub_total', 20, 2)->default(0);
            $table->string('payment_terms')->nullable();
            $table->string('bank_account')->nullable();
            $table->boolean('tax_included')->default(false);
            $table->timestamps();
        });

         Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotation')->onDelete('cascade');
            $table->string('description');
            $table->integer('qty')->default(0);
            $table->decimal('unit_price', 20, 2)->default(0);
            $table->decimal('total_price', 20, 2)->default(0);
            $table->timestamps();
        });

         Schema::create('quotation_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotation')->onDelete('cascade');
            $table->string('description');
            $table->boolean('responsible_pt_gpt')->default(false);
            $table->boolean('responsible_client')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_scopes');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotation');
    }
};
