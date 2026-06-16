<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_deposit_months', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->unsignedTinyInteger('month'); // 1-12
            $table->unsignedSmallInteger('year');
            $table->foreignId('savings_entry_id')->nullable()->constrained('savings_entries')->cascadeOnDelete();
            $table->timestamps();

            // Unique constraint to prevent duplicate month entries per member
            $table->unique(['member_id', 'month', 'year']);
            $table->index(['member_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_deposit_months');
    }
};
