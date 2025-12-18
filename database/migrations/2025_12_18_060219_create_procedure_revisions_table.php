<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procedure_revisions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procedure_id')->constrained('procedures')->cascadeOnDelete();

            // rev 0 = REV 00, rev 1 = REV 01, dst
            $table->unsignedInteger('rev_no')->default(0);

            $table->date('tanggal_rev')->nullable();
            $table->text('change_note')->nullable();

            $table->enum('status', [
                'pending',
                'pending_checked_by',
                'pending_approved_by',
                'approved',
                'rejected',
            ])->default('pending');

            // reject wajib alasan + siapa yang reject + kapan
            $table->text('reject_reason')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->unique(['procedure_id', 'rev_no']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedure_revisions');
    }
};
