<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('document_type', [
                'nid_copy',
                'birth_registration_copy',
                'trade_license',
                'passport_copy',
                'membership_agreement',
                'nominee_form',
                'bank_account_proof',
                'other_attachment'
            ]);
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->unsignedInteger('file_size');
            $table->string('mime_type', 100);
            $table->timestamp('upload_date');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->text('remarks')->nullable();
            $table->boolean('verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->date('verification_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('document_type');
            $table->index('verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
