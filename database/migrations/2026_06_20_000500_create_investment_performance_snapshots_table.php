<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_performance_snapshots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('investment_id');
            $table->date('snapshot_date')->index();
            $table->decimal('total_invested', 14, 2);
            $table->decimal('current_value', 14, 2);
            $table->decimal('unrealized_gain_loss', 14, 2);
            $table->decimal('realized_gain_loss', 14, 2);
            $table->decimal('return_percentage', 8, 2);
            $table->unsignedInteger('transaction_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Foreign key for investment
            $table->foreign('investment_id')->references('id')->on('investments')->cascadeOnDelete();

            // Indexes
            $table->index('investment_id', 'idx_inv_perf_investment');
            $table->index(['investment_id', 'snapshot_date'], 'idx_inv_perf_compound');
            $table->unique(['investment_id', 'snapshot_date'], 'unique_inv_perf_snapshot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_performance_snapshots');
    }
};
