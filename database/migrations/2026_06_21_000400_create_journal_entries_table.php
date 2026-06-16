<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')
                ->constrained('journal_vouchers')
                ->cascadeOnDelete();
            $table->foreignId('account_id')
                ->constrained('chart_of_accounts')
                ->restrictOnDelete();
            $table->decimal('debit_amount', 14, 2)->nullable();
            $table->decimal('credit_amount', 14, 2)->nullable();
            $table->text('description')->nullable();
            $table->integer('entry_sequence')->default(1);
            $table->timestamps();

            $table->index(['voucher_id', 'entry_sequence']);
            $table->index(['account_id', 'created_at']);
            $table->index(['voucher_id', 'account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
