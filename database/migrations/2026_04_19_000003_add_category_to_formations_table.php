<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('slug')
                ->constrained('formation_categories')
                ->nullOnDelete();

            $table->string('certification_type')->nullable()->after('category_id'); // PMP, CompTIA, Cisco...
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('intermediate')->after('certification_type');
            $table->boolean('is_featured')->default(false)->after('level');
            $table->string('language', 20)->default('fr')->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'certification_type', 'level', 'is_featured', 'language']);
        });
    }
};
