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
        Schema::create('organization_profile_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_profile_id')->constrained('organization_profiles')->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('action_type', ['created', 'updated', 'deleted', 'section_updated'])->default('updated');
            $table->string('section_name')->nullable(); // Which section was changed
            $table->string('field_name')->nullable(); // Which field was changed
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('timestamp')->useCurrent();

            // Indexes
            $table->index(['organization_profile_id', 'timestamp'], 'org_profile_ts_idx');
            $table->index('changed_by');
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_profile_audit_logs');
    }
};
