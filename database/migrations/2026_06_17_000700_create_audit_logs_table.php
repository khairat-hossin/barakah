<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->enum('action_type', [
                'created', 'updated', 'deleted', 'restored', 'status_changed',
                'allocated', 'transfer_initiated', 'transfer_approved', 'transfer_rejected',
                'allocation_changed', 'position_assigned', 'position_changed', 'position_ended',
                'position_removed', 'uploaded', 'verified'
            ]);
            $table->string('entity_type', 100); // Member, Share, Transfer, Nominee, Committee, Document
            $table->unsignedBigInteger('entity_id');
            $table->json('old_value')->nullable();
            $table->json('new_value')->nullable();
            $table->json('changes')->nullable(); // Field-level diff
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('timestamp')->useCurrent();

            $table->index('entity_type');
            $table->index('entity_id');
            $table->index('timestamp');
            $table->index('user_id');
            $table->index('action_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
