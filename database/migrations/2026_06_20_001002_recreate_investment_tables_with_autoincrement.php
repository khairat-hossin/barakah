<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Drop all investment tables
        Schema::dropIfExists('investments_accounting_entries');
        Schema::dropIfExists('investment_performance_snapshots');
        Schema::dropIfExists('investment_documents');
        Schema::dropIfExists('investment_status_histories');
        Schema::dropIfExists('investment_transactions');
        Schema::dropIfExists('investments');
        Schema::dropIfExists('investment_types');

        // Recreate investment_types with auto-increment ID
        Schema::create('investment_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->unsignedInteger('default_tenure_months')->nullable();
            $table->enum('default_return_type', ['fixed', 'variable', 'dividend'])->default('fixed');
            $table->boolean('requires_approval')->default(false);
            $table->decimal('max_investment_amount', 14, 2)->nullable();
            $table->decimal('min_investment_amount', 14, 2)->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
            $table->index('code', 'idx_inv_types_code');
        });

        // Recreate investments table
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
            $table->index('code', 'idx_investments_code');
            $table->index('investment_type_id', 'idx_investments_type');
            $table->index('investor_id', 'idx_investments_investor');
            $table->index('start_date', 'idx_investments_start_date');
            $table->index('maturity_date', 'idx_investments_maturity_date');
            $table->index('created_by', 'idx_investments_created_by');
            $table->index(['created_by', 'status', 'start_date'], 'idx_investments_compound');
        });

        // Recreate investment_transactions table
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
            $table->index('investment_id', 'idx_inv_tx_investment');
            $table->index('transaction_type', 'idx_inv_tx_type');
            $table->index('status', 'idx_inv_tx_status');
            $table->index('transaction_number', 'idx_inv_tx_number');
            $table->index(['investment_id', 'transaction_type', 'transaction_date'], 'idx_inv_tx_compound');
        });

        // Recreate investment_status_histories table
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
            $table->index('investment_id', 'idx_inv_sh_investment');
            $table->index('changed_at', 'idx_inv_sh_changed_at');
            $table->index(['status_from', 'status_to'], 'idx_inv_sh_transition');
        });

        // Recreate investment_documents table
        Schema::create('investment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $table->enum('document_type', [
                'agreement',
                'certificate',
                'statement',
                'proof_of_investment',
                'valuation',
                'contract',
                'legal_document',
                'other'
            ])->index();
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 100);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_public')->default(false);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->index('investment_id', 'idx_inv_doc_investment');
            $table->index('document_type', 'idx_inv_doc_type');
            $table->index('verified_at', 'idx_inv_doc_verified_at');
        });

        // Recreate investment_performance_snapshots table
        Schema::create('investment_performance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $table->date('snapshot_date')->index();
            $table->decimal('total_invested', 14, 2);
            $table->decimal('current_value', 14, 2);
            $table->decimal('unrealized_gain_loss', 14, 2);
            $table->decimal('realized_gain_loss', 14, 2);
            $table->decimal('return_percentage', 8, 2);
            $table->unsignedInteger('transaction_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('investment_id', 'idx_inv_perf_investment');
            $table->index(['investment_id', 'snapshot_date'], 'idx_inv_perf_compound');
            $table->unique(['investment_id', 'snapshot_date'], 'unique_inv_perf_snapshot');
        });

        // Recreate investments_accounting_entries table
        Schema::create('investments_accounting_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $table->foreignId('transaction_id')->nullable()->constrained('investment_transactions')->cascadeOnDelete();
            $table->string('journal_entry_number', 50)->nullable()->unique();
            $table->enum('entry_type', ['debit', 'credit']);
            $table->string('account_code', 50)->nullable();
            $table->string('account_name', 255);
            $table->decimal('amount', 14, 2);
            $table->string('currency_code', 3)->default('BDT');
            $table->enum('posting_status', ['draft', 'posted', 'reversed'])->default('draft')->index();
            $table->timestamp('posted_at')->nullable();
            $table->string('external_reference', 255)->nullable()->index();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->index(['investment_id', 'posting_status'], 'idx_inv_acct_compound');
            $table->index('journal_entry_number', 'idx_inv_acct_journal');
            $table->index('external_reference', 'idx_inv_acct_external');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // This migration cannot be safely reversed
    }
};
