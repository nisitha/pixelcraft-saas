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
            $table->decimal('profit_markup', 12, 2)->default(0.00)->after('tax');
            $table->string('profit_markup_type')->default('fixed')->after('profit_markup');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('profit_markup', 12, 2)->default(0.00)->after('tax');
            $table->string('profit_markup_type')->default('fixed')->after('profit_markup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['profit_markup', 'profit_markup_type']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['profit_markup', 'profit_markup_type']);
        });
    }
};
