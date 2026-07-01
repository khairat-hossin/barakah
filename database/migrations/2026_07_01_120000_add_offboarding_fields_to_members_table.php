<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->date('deactivated_at')->nullable()->after('status');
            $table->string('deactivation_reason', 30)->nullable()->after('deactivated_at'); // withdrawn, expelled, deceased, other
            $table->text('deactivation_note')->nullable()->after('deactivation_reason');
            $table->foreignId('deactivated_by')->nullable()->after('deactivation_note')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deactivated_by');
            $table->dropColumn(['deactivated_at', 'deactivation_reason', 'deactivation_note']);
        });
    }
};
