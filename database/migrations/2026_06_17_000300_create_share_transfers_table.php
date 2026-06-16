<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('to_member_id')->constrained('members')->cascadeOnDelete();
            $table->json('shares_json');
            $table->unsignedInteger('share_count');
            $table->date('transfer_date');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->date('approval_date')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('from_member_id');
            $table->index('to_member_id');
            $table->index('transfer_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_transfers');
    }
};
