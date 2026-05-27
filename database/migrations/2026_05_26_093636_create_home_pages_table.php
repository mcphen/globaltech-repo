<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_pages', function (Blueprint $table) {
            $table->id();
            // Hero
            $table->string('hero_badge')->nullable();
            $table->string('hero_title_1')->nullable();
            $table->string('hero_title_2')->nullable();
            $table->string('hero_title_3')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->json('hero_trust_badges')->nullable();
            $table->json('hero_mini_stats')->nullable();
            // Stats bar
            $table->json('stats')->nullable();
            // Pillars section
            $table->string('pillars_label')->nullable();
            $table->string('pillars_title')->nullable();
            $table->text('pillars_subtitle')->nullable();
            // B2B Corporate section
            $table->string('b2b_badge')->nullable();
            $table->string('b2b_title_1')->nullable();
            $table->string('b2b_title_2')->nullable();
            $table->text('b2b_subtitle')->nullable();
            $table->json('b2b_list_items')->nullable();
            $table->json('b2b_benefits')->nullable();
            // Why Us section
            $table->string('why_label')->nullable();
            $table->string('why_title')->nullable();
            $table->text('why_subtitle')->nullable();
            // Portfolio section
            $table->string('portfolio_label')->nullable();
            $table->string('portfolio_title')->nullable();
            // Testimonials section
            $table->string('testimonials_label')->nullable();
            $table->string('testimonials_title')->nullable();
            // News section
            $table->string('news_label')->nullable();
            $table->string('news_title')->nullable();
            // Final CTA
            $table->string('cta_badge')->nullable();
            $table->string('cta_title')->nullable();
            $table->string('cta_gradient_word')->nullable();
            $table->text('cta_subtitle')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_pages');
    }
};
