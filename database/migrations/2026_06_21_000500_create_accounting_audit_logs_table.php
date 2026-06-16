<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 100)->index();
            $table->unsignedBigInteger('entity_id')->index();
            $table->enum('action', ['CREATED', 'POSTED', 'REVERSED', 'UPDATED']);
            $table->foreignId('user_id')->constrained('users');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('timestamp')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
            $table->index(['user_id', 'timestamp']);
            $table->index(['timestamp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_audit_logs');
    }
};
