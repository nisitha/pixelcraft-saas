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
        Schema::table('quotations', function (Blueprint $table) {
            $table->boolean('is_sent')->default(false)->after('status');
            $table->timestamp('sent_at')->nullable()->after('is_sent');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->boolean('is_sent')->default(false)->after('status');
            $table->timestamp('sent_at')->nullable()->after('is_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['is_sent', 'sent_at']);
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['is_sent', 'sent_at']);
        });
    }
};
