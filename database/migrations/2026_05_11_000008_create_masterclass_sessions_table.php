<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masterclass_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('badge_text')->nullable();
            $table->string('cta_titre')->nullable();
            $table->text('cta_description')->nullable();
            $table->string('date_session')->nullable();
            $table->unsignedSmallInteger('places_total')->default(20);
            $table->unsignedSmallInteger('places_restantes')->default(0);
            $table->string('prix_a_partir')->nullable();
            $table->string('financement')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masterclass_sessions');
    }
};
