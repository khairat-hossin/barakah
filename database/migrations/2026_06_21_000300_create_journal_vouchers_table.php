<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number', 100)->unique()->index();
            $table->date('voucher_date')->index();
            $table->enum('voucher_type', ['DEPOSIT', 'EXPENSE', 'INVESTMENT', 'SHARE', 'MANUAL', 'REVERSAL']);
            $table->string('source_module', 100)->nullable()->index();
            $table->unsignedBigInteger('source_record_id')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['DRAFT', 'POSTED', 'REVERSED'])->default('DRAFT')->index();
            $table->timestamp('posted_date')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users');
            $table->timestamp('reversed_date')->nullable();
            $table->foreignId('reversed_by')->nullable()->constrained('users');
            $table->text('reversal_reason')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'voucher_date']);
            $table->index(['source_module', 'source_record_id']);
            $table->index(['created_by', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_vouchers');
    }
};
