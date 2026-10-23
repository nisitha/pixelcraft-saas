<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('is_internal_overhead')->default(false)->after('amount');
        });

        // Retroactively flag existing office overhead allocation expenses
        DB::table('expenses')
            ->where('reference_no', 'OFFICE-EXP-GAMINI')
            ->orWhere('title', 'like', '%Office Expenses Allocation%')
            ->orWhere('category', 'like', '%Office Overhead%')
            ->update(['is_internal_overhead' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('is_internal_overhead');
        });
    }
};
