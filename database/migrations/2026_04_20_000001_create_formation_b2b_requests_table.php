<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('formation_b2b_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();

            // Entreprise
            $table->string('company_name');
            $table->string('company_sector')->nullable();

            // Responsable / Contact RH
            $table->string('contact_first_name');
            $table->string('contact_last_name');
            $table->string('contact_function')->nullable();
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();

            // Participants (JSON array [{first_name, last_name, email}])
            $table->json('participants')->nullable();

            $table->text('attentes')->nullable();
            $table->string('status')->default('pending'); // pending | confirmed | cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_b2b_requests');
    }
};
