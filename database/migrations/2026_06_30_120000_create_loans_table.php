<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_code')->unique();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->decimal('loan_amount', 14, 2);
            $table->decimal('service_charge', 14, 2)->default(0);
            $table->date('taken_date');
            $table->date('due_date')->nullable();
            $table->string('status', 30)->default('pending'); // pending, active, repaid, rejected, written_off
            $table->string('purpose')->nullable();
            $table->text('comment')->nullable();
            $table->json('attachments')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('member_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
