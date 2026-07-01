<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('repaid_date');
            $table->unsignedBigInteger('payment_method_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('comment')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index('loan_id');
            $table->index('repaid_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
    }
};
