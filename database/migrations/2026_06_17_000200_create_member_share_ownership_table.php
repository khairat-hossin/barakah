<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_share_ownership', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->cascadeOnDelete();
            $table->foreignId('share_id')->constrained('shares')->cascadeOnDelete();
            $table->date('ownership_start_date');
            $table->date('ownership_end_date')->nullable();
            $table->unsignedBigInteger('transfer_reference')->nullable(); // Will add FK constraint in separate migration
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Only one current owner per share
            $table->unique(['share_id', 'ownership_end_date']);
            $table->index('member_id');
            $table->index('ownership_end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_share_ownership');
    }
};
