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
        Schema::create('product_serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('serial_number');
            $table->integer('warranty_months')->default(12);
            $table->date('warranty_expires_at')->nullable();
            $table->string('status')->default('in_stock'); // in_stock, sold, defective
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'serial_number']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'product_id']);
            $table->index(['company_id', 'contact_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_serials');
    }
};
