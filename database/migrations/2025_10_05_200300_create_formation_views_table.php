<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formation_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->string('ip_address', 45); // supports IPv6
            $table->date('viewed_date');
            $table->timestamps();

            $table->unique(['formation_id', 'ip_address', 'viewed_date'], 'uniq_formation_ip_date');
            $table->index(['formation_id', 'viewed_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_views');
    }
};
