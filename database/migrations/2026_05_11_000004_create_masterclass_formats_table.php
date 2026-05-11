<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masterclass_formats', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->default('bi-people-fill');
            $table->string('titre');
            $table->string('couleur')->default('#0B1437');
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masterclass_formats');
    }
};
