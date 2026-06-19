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
        Schema::table('members', function (Blueprint $table) {
            // Add only if column doesn't exist
            if (!Schema::hasColumn('members', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('members', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('members', 'postal_code')) {
                $table->string('postal_code')->nullable();
            }

            // Nominee Information
            if (!Schema::hasColumn('members', 'nominee_name')) {
                $table->string('nominee_name')->nullable();
            }
            if (!Schema::hasColumn('members', 'nominee_relation')) {
                $table->string('nominee_relation')->nullable();
            }
            if (!Schema::hasColumn('members', 'nominee_phone')) {
                $table->string('nominee_phone')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $columns = ['address', 'city', 'postal_code', 'nominee_name', 'nominee_relation', 'nominee_phone'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('members', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
