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
            $table->foreignId('sales_agent_id')->nullable()->after('contact_id')->constrained('sales_agents')->nullOnDelete();
            $table->decimal('commission_amount', 15, 2)->default(0.00)->after('grand_total');
            $table->string('commission_status')->default('pending')->after('commission_amount'); // pending, approved, paid
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('sales_agent_id')->nullable()->after('contact_id')->constrained('sales_agents')->nullOnDelete();
            $table->decimal('commission_amount', 15, 2)->default(0.00)->after('due_amount');
            $table->string('commission_status')->default('pending')->after('commission_amount'); // pending, approved, paid
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['sales_agent_id']);
            $table->dropColumn(['sales_agent_id', 'commission_amount', 'commission_status']);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['sales_agent_id']);
            $table->dropColumn(['sales_agent_id', 'commission_amount', 'commission_status']);
        });
    }
};
