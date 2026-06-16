<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investment_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('investment_id');
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

            // Foreign key for investment
            $table->foreign('investment_id')->references('id')->on('investments')->cascadeOnDelete();

            // Indexes
            $table->index('investment_id', 'idx_inv_doc_investment');
            $table->index('document_type', 'idx_inv_doc_type');
            $table->index('verified_at', 'idx_inv_doc_verified_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investment_documents');
    }
};
