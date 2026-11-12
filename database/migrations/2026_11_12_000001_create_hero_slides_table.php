<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image_path')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('text')->nullable();
            $table->string('button1_label')->nullable();
            $table->string('button1_url')->nullable();
            $table->string('button2_label')->nullable();
            $table->string('button2_url')->nullable();
            $table->string('text_position', 10)->default('left'); // left | center | right
            $table->unsignedTinyInteger('overlay_opacity')->default(50); // 0-90 (%)
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
