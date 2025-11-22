<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lead_formation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->text('attentes')->nullable();
            $table->timestamps();
            $table->unique(['lead_id', 'formation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_formation');
    }
};
