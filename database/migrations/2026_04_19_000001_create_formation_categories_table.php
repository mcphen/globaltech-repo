<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();           // bootstrap-icons class
            $table->string('color', 20)->nullable();      // hex color
            $table->string('background', 20)->nullable(); // hex background
            $table->text('description')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default categories
        DB::table('formation_categories')->insert([
            ['name' => 'Informatique & IT', 'slug' => 'it', 'icon' => 'bi-cpu', 'color' => '#2563EB', 'background' => '#EFF6FF', 'is_featured' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PMP & Gestion de Projet', 'slug' => 'pmp', 'icon' => 'bi-diagram-3', 'color' => '#16A34A', 'background' => '#F0FDF4', 'is_featured' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Management & Leadership', 'slug' => 'management', 'icon' => 'bi-people', 'color' => '#D97706', 'background' => '#FFFBEB', 'is_featured' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance & Comptabilité', 'slug' => 'finance', 'icon' => 'bi-graph-up', 'color' => '#7C3AED', 'background' => '#F5F3FF', 'is_featured' => false, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Autres Certifications', 'slug' => 'other', 'icon' => 'bi-award', 'color' => '#475569', 'background' => '#F8FAFC', 'is_featured' => false, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_categories');
    }
};
