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
        if (Schema::hasTable('invoices') && ! Schema::hasIndex('invoices', ['company_id', 'invoice_date', 'status'])) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->index(['company_id', 'invoice_date', 'status'], 'invoices_company_date_status_idx');
            });
        }

        if (Schema::hasTable('invoice_items') && ! Schema::hasIndex('invoice_items', ['invoice_id', 'cost_price'])) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->index(['invoice_id', 'cost_price'], 'invoice_items_invoice_cost_idx');
            });
        }

        if (Schema::hasTable('payments') && ! Schema::hasIndex('payments', ['company_id', 'payment_date'])) {
            Schema::table('payments', function (Blueprint $table) {
                $table->index(['company_id', 'payment_date'], 'payments_company_payment_date_idx');
            });
        }

        if (Schema::hasTable('expenses') && ! Schema::hasIndex('expenses', ['company_id', 'expense_date'])) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->index(['company_id', 'expense_date'], 'expenses_company_expense_date_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices') && Schema::hasIndex('invoices', 'invoices_company_date_status_idx')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->dropIndex('invoices_company_date_status_idx');
            });
        }

        if (Schema::hasTable('invoice_items') && Schema::hasIndex('invoice_items', 'invoice_items_invoice_cost_idx')) {
            Schema::table('invoice_items', function (Blueprint $table) {
                $table->dropIndex('invoice_items_invoice_cost_idx');
            });
        }
    }
};
