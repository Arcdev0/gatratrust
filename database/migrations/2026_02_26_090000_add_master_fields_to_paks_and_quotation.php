<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paks', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('date');
            $table->text('customer_address')->nullable()->after('customer_name');
            $table->string('attention')->nullable()->after('customer_address');
            $table->string('your_reference')->nullable()->after('attention');
            $table->text('terms_text')->nullable()->after('your_reference');
        });

        Schema::create('pak_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pak_id')->constrained('paks')->cascadeOnDelete();
            $table->string('description');
            $table->boolean('responsible_pt_gpt')->default(false);
            $table->boolean('responsible_client')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['pak_id', 'sort_order']);
        });

        Schema::create('pak_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pak_id')->constrained('paks')->cascadeOnDelete();
            $table->text('description');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['pak_id', 'sort_order']);
        });

        Schema::table('quotation', function (Blueprint $table) {
            $table->foreignId('pak_id')->nullable()->after('id')->constrained('paks')->nullOnDelete();
            $table->index('pak_id');
        });
    }

    public function down(): void
    {
        Schema::table('quotation', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pak_id');
        });

        Schema::dropIfExists('pak_terms');
        Schema::dropIfExists('pak_scopes');

        Schema::table('paks', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'customer_address', 'attention', 'your_reference', 'terms_text']);
        });
    }
};
