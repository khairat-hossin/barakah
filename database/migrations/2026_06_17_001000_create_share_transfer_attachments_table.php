<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_transfer_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transfer_id')->constrained('share_transfers')->cascadeOnDelete();
            $table->string('file_path', 500);
            $table->string('file_name', 255);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();

            $table->index('transfer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_transfer_attachments');
    }
};
