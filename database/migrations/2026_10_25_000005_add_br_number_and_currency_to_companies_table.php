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
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (! Schema::hasColumn('companies', 'br_number')) {
                    $table->string('br_number')->nullable()->after('slug');
                }
                if (! Schema::hasColumn('companies', 'currency')) {
                    $table->char('currency', 3)->default('LKR')->after('email');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('companies')) {
            Schema::table('companies', function (Blueprint $table) {
                if (Schema::hasColumn('companies', 'currency')) {
                    $table->dropColumn('currency');
                }
                if (Schema::hasColumn('companies', 'br_number')) {
                    $table->dropColumn('br_number');
                }
            });
        }
    }
};
