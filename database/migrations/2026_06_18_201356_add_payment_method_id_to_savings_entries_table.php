<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('savings_entries', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('payment_method')->constrained('payment_methods')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('savings_entries', function (Blueprint $table) {
            if (Schema::hasColumn('savings_entries', 'payment_method_id')) {
                try {
                    DB::statement('ALTER TABLE savings_entries DROP FOREIGN KEY savings_entries_payment_method_id_foreign');
                } catch (\Exception $e) {
                    // Foreign key may not exist
                }
                $table->dropColumn('payment_method_id');
            }
        });
    }
};
