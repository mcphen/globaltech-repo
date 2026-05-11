<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masterclass_domaines', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->default('bi-star');
            $table->string('color')->default('#E8A020');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('niveau')->nullable();
            $table->string('duree')->nullable();
            $table->json('modules')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masterclass_domaines');
    }
};
