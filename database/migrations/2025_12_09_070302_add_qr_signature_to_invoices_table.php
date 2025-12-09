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
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('approved_qr')->nullable()->after('approval_status');      // path file QR
            $table->string('signature_token', 64)->nullable()->after('approved_qr');  // token verifikasi
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['approved_qr', 'signature_token']);
        });
    }
};
