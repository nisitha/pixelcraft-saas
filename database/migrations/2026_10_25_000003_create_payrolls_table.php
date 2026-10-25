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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('month'); // "YYYY-MM", e.g. "2026-10"
            $table->string('title');
            $table->decimal('total_basic', 12, 2)->default(0.00);
            $table->decimal('total_allowances', 12, 2)->default(0.00);
            $table->decimal('total_gross', 12, 2)->default(0.00);
            $table->decimal('total_epf_employee', 12, 2)->default(0.00); // 8%
            $table->decimal('total_epf_employer', 12, 2)->default(0.00); // 12%
            $table->decimal('total_etf_employer', 12, 2)->default(0.00); // 3%
            $table->decimal('total_advances', 12, 2)->default(0.00);
            $table->decimal('total_deductions', 12, 2)->default(0.00);
            $table->decimal('total_net_payable', 12, 2)->default(0.00);
            $table->string('status')->default('draft'); // 'draft', 'confirmed', 'paid'
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
