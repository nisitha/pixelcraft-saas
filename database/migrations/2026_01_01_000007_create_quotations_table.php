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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('quote_number');
            $table->date('quote_date');
            $table->date('valid_until')->nullable();
            $table->decimal('system_kw', 8, 2)->nullable();
            $table->string('phase_type')->default('single'); // single, three
            $table->string('roof_type')->nullable(); // Tile, Asbestos, Zinc-Alum, Flat Concrete
            $table->string('inverter_brand')->nullable();
            $table->string('panel_brand')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('tax', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->text('terms')->nullable();
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected, invoiced
            $table->timestamps();

            $table->index(['company_id', 'quote_number']);
            $table->index(['company_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
