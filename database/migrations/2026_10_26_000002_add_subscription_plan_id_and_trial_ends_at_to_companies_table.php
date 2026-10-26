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
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('subscription_plan_id')
                ->nullable()
                ->after('plan_id')
                ->constrained('subscription_plans')
                ->nullOnDelete();
            $table->timestamp('trial_ends_at')
                ->nullable()
                ->after('subscription_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subscription_plan_id');
            $table->dropColumn('trial_ends_at');
        });
    }
};
