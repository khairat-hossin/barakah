<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_types');
    }
};
