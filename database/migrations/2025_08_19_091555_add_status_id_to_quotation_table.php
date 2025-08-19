<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('id');
            $table->foreign('status_id')
                ->references('id')
                ->on('status')
                ->onDelete('set null');

            $table->text('rejected_reason')->nullable()->after('status_id');

            // kolom baru
            $table->unsignedBigInteger('approved_by')->nullable()->after('rejected_reason');
            $table->text('approved_qr')->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('approved_qr');

            // relasi approved_by ke users
            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
            $table->dropColumn('rejected_reason');

            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_qr');
            $table->dropColumn('approved_at');
        });
    }

};
