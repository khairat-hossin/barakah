<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominee_audit_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nominee_id')->nullable()->constrained('nominees');
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('action', ['created', 'updated', 'deleted']);
            $table->string('full_name', 255);
            $table->unsignedTinyInteger('allocation_percentage');
            $table->unsignedTinyInteger('total_allocation_after_change');
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('timestamp')->useCurrent();

            $table->index('member_id');
            $table->index('timestamp');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominee_audit_histories');
    }
};
