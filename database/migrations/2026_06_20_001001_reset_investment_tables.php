<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Truncate all investment tables
        DB::table('investments_accounting_entries')->truncate();
        DB::table('investment_performance_snapshots')->truncate();
        DB::table('investment_documents')->truncate();
        DB::table('investment_status_histories')->truncate();
        DB::table('investment_transactions')->truncate();
        DB::table('investments')->truncate();
        DB::table('investment_types')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Cannot reverse this operation
    }
};
