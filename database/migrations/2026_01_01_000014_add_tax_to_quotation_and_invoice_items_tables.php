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
        if (Schema::hasTable('quotation_items') && ! Schema::hasColumn('quotation_items', 'tax')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->decimal('tax', 12, 2)->default(0.00)->after('unit_price');
            });
        }

        if (Schema::hasTable('invoice_items') && ! Schema::hasColumn('invoice_items', 'tax')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->decimal('tax', 12, 2)->default(0.00)->after('unit_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quotation_items') && Schema::hasColumn('quotation_items', 'tax')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->dropColumn('tax');
            });
        }

        if (Schema::hasTable('invoice_items') && Schema::hasColumn('invoice_items', 'tax')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropColumn('tax');
            });
        }
    }
};
