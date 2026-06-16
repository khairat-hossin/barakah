<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_code', 100)->unique()->index();
            $table->string('event_name', 255);
            $table->enum('event_type', ['DEPOSIT', 'EXPENSE', 'INVESTMENT', 'SHARE', 'OTHER']);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['event_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_events');
    }
};
