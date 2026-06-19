<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if projects table doesn't exist (fresh install)
        if (!Schema::hasTable('projects')) {
            return;
        }

        // Drop foreign key first
        try {
            DB::statement('ALTER TABLE expenses DROP FOREIGN KEY expenses_project_id_foreign');
        } catch (\Exception $e) {
            // Foreign key doesn't exist
        }

        // Drop project_id column from expenses
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'project_id')) {
                $table->dropColumn('project_id');
            }
        });

        // Drop projects table
        Schema::dropIfExists('projects');
    }

    public function down(): void
    {
        // Re-create projects table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->decimal('budget_requested', 14, 2)->default(0);
            $table->integer('progress_percentage')->default(0);
            $table->string('status')->default('draft');
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->decimal('expected_return_percentage', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // Re-add project_id to expenses
        Schema::table('expenses', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            $table->index('project_id');
        });
    }
};
