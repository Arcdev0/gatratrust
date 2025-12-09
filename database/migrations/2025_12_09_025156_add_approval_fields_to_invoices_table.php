<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            // status approval: pending / approved / rejected
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('status'); // atau setelah kolom mana saja

            // user yang melakukan approve / reject
            $table->unsignedBigInteger('user_approve')->nullable()->after('approval_status');

            // waktu approve & reject
            $table->timestamp('approved_at')->nullable()->after('user_approve');
            $table->timestamp('rejected_at')->nullable()->after('approved_at');

            // alasan reject
            $table->text('reject_reason')->nullable()->after('rejected_at');

            // (opsional) FK ke users
            $table->foreign('user_approve')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['user_approve']);
            $table->dropColumn([
                'approval_status',
                'user_approve',
                'approved_at',
                'rejected_at',
                'reject_reason',
            ]);
        });
    }
};
