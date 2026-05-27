<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('abouts', function (Blueprint $table) {
            // Hero
            $table->string('hero_badge')->nullable();
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            // Section Notre Histoire
            $table->string('history_label')->nullable();
            $table->string('history_title')->nullable();
            $table->json('stats')->nullable();
            $table->json('values')->nullable();
            // Timeline
            $table->string('timeline_label')->nullable();
            $table->string('timeline_title')->nullable();
            $table->json('milestones')->nullable();
            // Bureaux
            $table->string('offices_title')->nullable();
            $table->text('offices_subtitle')->nullable();
            $table->json('offices')->nullable();
            // CTA
            $table->string('cta_title')->nullable();
            $table->text('cta_subtitle')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('abouts', function (Blueprint $table) {
            $table->dropColumn([
                'hero_badge', 'hero_title', 'hero_subtitle',
                'history_label', 'history_title', 'stats', 'values',
                'timeline_label', 'timeline_title', 'milestones',
                'offices_title', 'offices_subtitle', 'offices',
                'cta_title', 'cta_subtitle',
            ]);
        });
    }
};
