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
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('fixed_allowance', 10, 2)->default(0.00);
            $table->decimal('additional_allowance', 10, 2)->default(0.00); // site OT / project bonus
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('epf_employee', 10, 2); // 8% on basic
            $table->decimal('epf_employer', 10, 2); // 12% on basic
            $table->decimal('etf_employer', 10, 2); // 3% on basic
            $table->decimal('advance_deduction', 10, 2)->default(0.00);
            $table->decimal('other_deductions', 10, 2)->default(0.00);
            $table->decimal('total_deductions', 10, 2); // epf_employee + advance_deduction + other_deductions
            $table->decimal('net_salary', 10, 2); // gross_salary - total_deductions
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'payroll_id']);
            $table->index(['payroll_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
