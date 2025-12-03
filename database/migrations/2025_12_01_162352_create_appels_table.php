<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appels', function (Blueprint $table) {
            $table->id();
             // Relation polymorphique
            $table->nullableMorphs('callable'); // callable_id, callable_type
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Date et heure de l'appel
            $table->dateTime('called_at')->nullable();
             // Type d'appel
            $table->enum('type', ['entrant', 'sortant'])->default('sortant');
            $table->enum('status', ['répondu', 'non-répondu', 'rappel-prévu'])->default('répondu');
            $table->integer('duration')->nullable();
            
            // Résumé ou notes
            $table->text('notes')->nullable();
            // Pour planifier un rappel
            $table->dateTime('next_call_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appels');
    }
};
