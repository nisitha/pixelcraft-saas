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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->string('method')->default('cash'); // cash, bank_transfer, card, cheque
            $table->string('reference_no')->nullable();
            $table->string('milestone_label')->nullable(); // e.g. 50% Advance, 40% Delivery, 10% Grid Connection
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
