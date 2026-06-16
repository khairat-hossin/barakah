<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique();
            $table->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $table->enum('transaction_type', [
                'INITIAL_INVESTMENT',
                'ADDITIONAL_INVESTMENT',
                'PROFIT_DISTRIBUTION',
                'LOSS_ADJUSTMENT',
                'WITHDRAWAL',
                'MATURITY_CLOSURE',
                'ADMINISTRATIVE_ADJUSTMENT',
                'DIVIDEND_PAYMENT',
                'REINVESTMENT',
            ])->index();
            $table->date('transaction_date')->index();
            $table->decimal('amount', 14, 2);
            $table->string('reference_number', 50)->nullable();
            $table->text('description');
            $table->enum('status', ['pending', 'processed', 'reversed', 'failed'])->default('processed')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index('investment_id', 'idx_inv_tx_investment');
            $table->index('transaction_type', 'idx_inv_tx_type');
            $table->index('status', 'idx_inv_tx_status');
            $table->index('transaction_number', 'idx_inv_tx_number');
            $table->index(['investment_id', 'transaction_type', 'transaction_date'], 'idx_inv_tx_compound');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_transactions');
    }
};
