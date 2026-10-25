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
        $tables = [
            'invoices',
            'quotations',
            'employees',
            'salary_advances',
            'payrolls',
            'expenses',
            'customers',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                if (! Schema::hasColumn($tableName, 'company_id')) {
                    Schema::table($tableName, function (Blueprint $table) {
                        $table->foreignId('company_id')
                            ->after('id')
                            ->constrained('companies')
                            ->cascadeOnDelete();
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'customers',
            'expenses',
            'payrolls',
            'salary_advances',
            'employees',
            'quotations',
            'invoices',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};
