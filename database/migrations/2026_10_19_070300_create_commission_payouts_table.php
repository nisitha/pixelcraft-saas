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
        Schema::create('commission_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();

            $table->string('voucher_number')->index();
            $table->date('payout_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('bank_transfer'); // cash, bank_transfer, cheque
            $table->string('reference_no')->nullable();
            $table->string('status')->default('paid'); // draft, paid
            $table->text('notes')->nullable();

            $table->string('paper_size')->default('a4');
            $table->string('orientation')->default('portrait');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_payouts');
    }
};
