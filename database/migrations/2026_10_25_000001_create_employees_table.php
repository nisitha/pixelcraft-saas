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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('calling_name')->nullable();
            $table->string('nic_number')->nullable();
            $table->string('designation');
            $table->string('contact_number')->nullable();
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->decimal('basic_salary', 10, 2)->default(0.00);
            $table->decimal('fixed_allowance', 10, 2)->default(0.00);
            $table->string('epf_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->date('joined_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
