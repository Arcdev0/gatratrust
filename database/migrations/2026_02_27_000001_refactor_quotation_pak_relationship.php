<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paks', function (Blueprint $table) {
            $table->foreignId('customer_user_id')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->index('customer_user_id');
        });

        $quotationsWithoutPak = DB::table('quotation')->whereNull('pak_id')->get();

        foreach ($quotationsWithoutPak as $quotation) {
            $pakId = DB::table('paks')->insertGetId([
                'pak_name' => 'LEGACY QUOTATION '.$quotation->quo_no,
                'pak_number' => 'LEGACY-PAK-'.$quotation->id,
                'pak_value' => (int) ($quotation->sub_total ?? $quotation->total_amount ?? 0),
                'location' => 'dalam_kota',
                'date' => $quotation->date,
                'customer_name' => $quotation->customer_name,
                'customer_address' => $quotation->customer_address,
                'attention' => $quotation->attention,
                'your_reference' => $quotation->your_reference,
                'terms_text' => $quotation->terms,
                'total_pak_cost' => (int) ($quotation->sub_total ?? $quotation->total_amount ?? 0),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('quotation')->where('id', $quotation->id)->update(['pak_id' => $pakId]);
        }

        Schema::table('quotation', function (Blueprint $table) {
            $table->dropForeign(['pak_id']);
            $table->foreignId('pak_id')->nullable(false)->change();
            $table->foreign('pak_id')->references('id')->on('paks')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->dropForeign(['pak_id']);
            $table->foreignId('pak_id')->nullable()->change();
            $table->foreign('pak_id')->references('id')->on('paks')->nullOnDelete();
        });

        Schema::table('paks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer_user_id');
        });
    }
};
