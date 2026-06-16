<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_allocation_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->cascadeOnDelete();
            $table->unsignedInteger('allocated_shares_count')->default(0);
            $table->decimal('ownership_percentage', 5, 2)->default(0);
            $table->unsignedTinyInteger('nominee_total_percentage')->default(0);
            $table->boolean('has_nominees')->default(false);
            $table->date('last_transfer_date')->nullable();
            $table->timestamps();

            $table->index('member_id');
            $table->index('allocated_shares_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_allocation_history');
    }
};
