<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formation_categories', function (Blueprint $table) {
            $table->string('label')->nullable()->after('name'); // Short tag shown above the card title
        });

        // Backfill default labels for seeded categories
        DB::table('formation_categories')->where('slug', 'it')->update(['label' => 'Programme Star']);
        DB::table('formation_categories')->where('slug', 'pmp')->update(['label' => 'PMP & Gestion']);
        DB::table('formation_categories')->where('slug', 'management')->update(['label' => 'Leadership']);
        DB::table('formation_categories')->where('slug', 'finance')->update(['label' => 'Finance']);
        DB::table('formation_categories')->where('slug', 'other')->update(['label' => 'Certifications']);
    }

    public function down(): void
    {
        Schema::table('formation_categories', function (Blueprint $table) {
            $table->dropColumn('label');
        });
    }
};
