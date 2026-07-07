<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organization_profiles', function (Blueprint $table) {
            // The month the association began collecting monthly deposits.
            // Member dues are accrued from this month for everyone.
            $table->date('deposit_start_month')->nullable()->after('share_face_value');
        });
    }

    public function down(): void
    {
        Schema::table('organization_profiles', function (Blueprint $table) {
            $table->dropColumn('deposit_start_month');
        });
    }
};
