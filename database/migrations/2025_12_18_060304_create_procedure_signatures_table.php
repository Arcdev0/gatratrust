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
        Schema::create('procedure_signatures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('procedure_revision_id')
                ->constrained('procedure_revisions')
                ->cascadeOnDelete();

            $table->enum('role', ['prepared_by', 'checked_by', 'approved_by']);

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('signed_at')->nullable();

            // optional: simpan catatan per tanda tangan (misal komentar checker)
            $table->text('note')->nullable();

            $table->unique(['procedure_revision_id', 'role']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedure_signatures');
    }
};
