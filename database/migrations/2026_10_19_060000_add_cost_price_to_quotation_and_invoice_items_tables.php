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
        if (Schema::hasTable('quotation_items') && ! Schema::hasColumn('quotation_items', 'cost_price')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->decimal('cost_price', 15, 2)->default(0.00)->after('product_id');
            });
        }

        if (Schema::hasTable('invoice_items') && ! Schema::hasColumn('invoice_items', 'cost_price')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->decimal('cost_price', 15, 2)->default(0.00)->after('product_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quotation_items') && Schema::hasColumn('quotation_items', 'cost_price')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->dropColumn('cost_price');
            });
        }

        if (Schema::hasTable('invoice_items') && Schema::hasColumn('invoice_items', 'cost_price')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropColumn('cost_price');
            });
        }
    }
};
