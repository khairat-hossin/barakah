<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('executive_committee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->enum('position', [
                'president',
                'senior_vice_president',
                'vice_president',
                'general_secretary',
                'joint_general_secretary',
                'treasurer',
                'auditor',
                'organizing_secretary',
                'publicity_secretary',
                'office_secretary',
                'executive_member'
            ]);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->index('member_id');
            $table->index('position');
            $table->index('end_date');
            // Only one current holder per exclusive position
            $table->unique(['position', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('executive_committee');
    }
};
