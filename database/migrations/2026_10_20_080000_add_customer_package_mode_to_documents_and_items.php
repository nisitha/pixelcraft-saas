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
            $table->string('price_display_mode')->default('itemized')->after('status');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('price_display_mode')->default('itemized')->after('status');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->string('warranty_text')->nullable()->after('description');
            $table->boolean('hide_on_customer_pdf')->default(false)->after('subtotal');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('warranty_text')->nullable()->after('description');
            $table->boolean('hide_on_customer_pdf')->default(false)->after('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('price_display_mode');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('price_display_mode');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn(['warranty_text', 'hide_on_customer_pdf']);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn(['warranty_text', 'hide_on_customer_pdf']);
        });
    }
};
