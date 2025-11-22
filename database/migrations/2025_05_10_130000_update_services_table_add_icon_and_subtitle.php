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
        Schema::table('services', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('title');
            $table->string('subtitle')->nullable()->after('icon');
            // Rename description to match our needs
            $table->renameColumn('description', 'description_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('icon');
            $table->dropColumn('subtitle');
            $table->renameColumn('description_old', 'description');
        });
    }
};
