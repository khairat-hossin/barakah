<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_event_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')
                ->constrained('accounting_events')
                ->cascadeOnDelete();
            $table->foreignId('debit_account_id')
                ->constrained('chart_of_accounts')
                ->restrictOnDelete();
            $table->foreignId('credit_account_id')
                ->constrained('chart_of_accounts')
                ->restrictOnDelete();
            $table->decimal('debit_multiplier', 3, 1)->default(1.0);
            $table->decimal('credit_multiplier', 3, 1)->default(1.0);
            $table->integer('sequence')->default(1);
            $table->timestamps();

            $table->index(['event_id', 'sequence']);
            $table->unique(['event_id', 'debit_account_id', 'credit_account_id', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_event_mappings');
    }
};
