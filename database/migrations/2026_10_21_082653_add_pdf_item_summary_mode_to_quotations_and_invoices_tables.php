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
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('pdf_item_summary_mode')->default('consolidated')->after('price_display_mode');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('pdf_item_summary_mode')->default('consolidated')->after('price_display_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('pdf_item_summary_mode');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('pdf_item_summary_mode');
        });
    }
};
