<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('full_name', 255);
            $table->string('father_name', 255)->nullable();
            $table->string('mother_name', 255)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nid_number', 50)->nullable()->unique();
            $table->string('birth_registration', 50)->nullable()->unique();
            $table->enum('relationship', ['son', 'daughter', 'wife', 'husband', 'parent', 'sibling', 'other']);
            $table->string('mobile_number', 20)->nullable()->unique();
            $table->string('email', 255)->nullable()->unique();
            $table->text('address')->nullable();
            $table->unsignedTinyInteger('allocation_percentage');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('allocation_percentage');
            $table->unique(['member_id', 'nid_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominees');
    }
};
