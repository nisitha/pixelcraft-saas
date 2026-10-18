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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('sku')->nullable();
            $table->string('type')->default('product'); // product, service, solar_package
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('cost_price', 12, 2)->default(0.00);
            $table->decimal('stock_qty', 10, 2)->default(0.00);
            $table->decimal('alert_qty', 10, 2)->default(0.00);
            $table->boolean('track_serial')->default(false);
            $table->unsignedInteger('warranty_months')->default(0);
            $table->timestamps();

            $table->index(['company_id', 'sku']);
            $table->index(['company_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
