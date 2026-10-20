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
        Schema::table('subscription_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('subscription_requests', 'payment_method')) {
                $table->string('payment_method')->default('bank_transfer')->after('amount');
            }
            if (! Schema::hasColumn('subscription_requests', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('payment_reference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscription_requests', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'receipt_path']);
        });
    }
};
