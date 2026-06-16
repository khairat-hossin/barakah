<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investments_accounting_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('investment_id');
            $table->uuid('transaction_id')->nullable();
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

            // Foreign keys
            $table->foreign('investment_id')->references('id')->on('investments')->cascadeOnDelete();
            $table->foreign('transaction_id')->references('id')->on('investment_transactions')->cascadeOnDelete();

            // Indexes
            $table->index(['investment_id', 'posting_status'], 'idx_inv_acct_compound');
            $table->index('journal_entry_number', 'idx_inv_acct_journal');
            $table->index('external_reference', 'idx_inv_acct_external');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments_accounting_entries');
    }
};
