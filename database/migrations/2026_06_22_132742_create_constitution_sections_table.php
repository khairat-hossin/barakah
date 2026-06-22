<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('constitution_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->longText('body')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Seed the existing placeholder content so the page isn't empty.
        $file = resource_path('data/constitution.php');
        if (is_file($file)) {
            $data = require $file;
            $order = 0;
            foreach (($data['sections'] ?? []) as $s) {
                DB::table('constitution_sections')->insert([
                    'title' => html_entity_decode($s['title'] ?? 'Section'),
                    'slug' => $s['id'] ?? \Illuminate\Support\Str::slug($s['title'] ?? 'section-' . $order),
                    'icon' => $s['icon'] ?? 'file-text',
                    'body' => $s['body'] ?? '',
                    'sort_order' => $order++,
                    'is_published' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('constitution_sections');
    }
};
