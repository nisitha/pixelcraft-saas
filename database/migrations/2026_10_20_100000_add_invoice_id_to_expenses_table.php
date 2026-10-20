<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (! Schema::hasColumn('expenses', 'invoice_id')) {
                $table->foreignId('invoice_id')->nullable()->after('company_id')->constrained('invoices')->nullOnDelete();
            }
            if (! Schema::hasColumn('expenses', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->after('invoice_id')->constrained('contacts')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'supplier_id')) {
                $table->dropConstrainedForeignId('supplier_id');
            }
            if (Schema::hasColumn('expenses', 'invoice_id')) {
                $table->dropConstrainedForeignId('invoice_id');
            }
        });
    }
};
