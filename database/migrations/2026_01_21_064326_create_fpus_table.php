<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fpus', function (Blueprint $table) {
            $table->id();

            $table->string('fpu_no', 50)->unique();

            $table->unsignedBigInteger('project_id')->nullable();
            $table->date('request_date');

            $table->unsignedBigInteger('requester_id')->nullable();
            $table->string('requester_name', 150)->nullable();

            // ✅ ENUM PURPOSE
            $table->enum('purpose', [
                'tagihan_rutin',
                'pembelian_material',
                'akomodasi_operasional',
                'bayar_vendor',
                'lainnya'
            ])->nullable();

            $table->text('notes')->nullable();

            // Wallet (COA) dipilih sebelum / saat approve
            $table->unsignedBigInteger('wallet_coa_id')->nullable();

            $table->decimal('total_amount', 18, 2)->default(0);

            // ✅ ENUM STATUS
            $table->enum('status', [
                'draft',
                'submitted',
                'approved',
                'rejected',
                'paid',
                'cancelled'
            ])->default('draft');

            // Approval tracking
            $table->timestamp('submitted_at')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejected_reason')->nullable();

            $table->timestamps();

            // FK
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            $table->foreign('requester_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('wallet_coa_id')->references('id')->on('coa')->nullOnDelete();

            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('rejected_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['status', 'request_date']);
            $table->index(['project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fpus');
    }
};
