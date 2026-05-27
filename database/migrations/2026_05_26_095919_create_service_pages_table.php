<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_pages', function (Blueprint $table) {
            $table->id();
            // Hero
            $table->string('hero_badge')->nullable();
            $table->string('hero_title')->nullable();
            $table->string('hero_gradient_word')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->json('hero_stats')->nullable();
            // Section services DB
            $table->string('services_label')->nullable();
            $table->string('services_title')->nullable();
            // Section offres de conseil
            $table->string('consulting_label')->nullable();
            $table->string('consulting_title')->nullable();
            $table->text('consulting_subtitle')->nullable();
            $table->json('consulting_offers')->nullable();
            // Section process
            $table->string('process_label')->nullable();
            $table->string('process_title')->nullable();
            $table->json('process_steps')->nullable();
            // CTA
            $table->string('cta_title')->nullable();
            $table->text('cta_subtitle')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_pages');
    }
};
