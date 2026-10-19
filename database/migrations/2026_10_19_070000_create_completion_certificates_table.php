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
        Schema::create('completion_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained()->nullOnDelete();

            $table->string('certificate_number')->index();
            $table->string('customer_name');
            $table->string('system_capacity');
            $table->text('site_address');
            $table->date('commissioning_date');
            $table->string('grid_connection')->default('On-Grid Net Metering');
            $table->string('warranty_period')->default('30 Years (Panels) / 10 Years (Inverter+Battery)');
            $table->string('system_status')->default('Commissioned & Operational');
            $table->string('verification_status')->default('Engineering Safety Compliant');
            $table->text('compliance_note')->default('Installed, tested, and commissioned in accordance with standard safety regulations.');

            $table->string('lead_engineer_name')->default('Lead Engineer');
            $table->string('lead_engineer_title')->default('Technical Operations');
            $table->string('director_name')->default('Managing Director');
            $table->string('director_title')->default('Vertex Home Solutions (Pvt) Ltd');
            $table->date('signed_date')->nullable();

            $table->string('paper_size')->default('a4'); // a4, a5, letter, b5
            $table->string('orientation')->default('landscape'); // landscape, portrait
            $table->string('status')->default('draft'); // draft, issued

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completion_certificates');
    }
};
