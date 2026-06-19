<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('investment_type_id')->constrained('investment_types');
            $table->foreignId('investor_id')->nullable()->constrained('members');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'active', 'matured', 'closed', 'suspended', 'cancelled'])->default('draft')->index();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->enum('return_type', ['fixed', 'variable', 'dividend'])->default('fixed');
            $table->decimal('expected_return_percentage', 5, 2)->nullable();
            $table->decimal('actual_return_percentage', 5, 2)->nullable();
            $table->unsignedInteger('tenure_months')->nullable();
            $table->date('start_date');
            $table->date('maturity_date')->nullable();
            $table->date('closed_date')->nullable();
            $table->decimal('total_invested_amount', 14, 2)->default(0);
            $table->decimal('total_returned_amount', 14, 2)->default(0);
            $table->decimal('net_profit_loss', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('code', 'idx_investments_code');
            $table->index('investment_type_id', 'idx_investments_type');
            $table->index('investor_id', 'idx_investments_investor');
            $table->index('start_date', 'idx_investments_start_date');
            $table->index('maturity_date', 'idx_investments_maturity_date');
            $table->index('created_by', 'idx_investments_created_by');
            $table->index(['created_by', 'status', 'start_date'], 'idx_investments_compound');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
