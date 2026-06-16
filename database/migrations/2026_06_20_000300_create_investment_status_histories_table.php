<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $table->enum('status_from', ['draft', 'active', 'matured', 'closed', 'suspended', 'cancelled']);
            $table->enum('status_to', ['draft', 'active', 'matured', 'closed', 'suspended', 'cancelled']);
            $table->string('reason', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('changed_at')->useCurrent();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index('investment_id', 'idx_inv_sh_investment');
            $table->index('changed_at', 'idx_inv_sh_changed_at');
            $table->index(['status_from', 'status_to'], 'idx_inv_sh_transition');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_status_histories');
    }
};
