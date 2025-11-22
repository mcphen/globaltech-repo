<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('formations')) {
            Schema::table('formations', function (Blueprint $table) {
                if (!Schema::hasColumn('formations', 'slug')) {
                    $table->string('slug')->nullable()->unique()->after('title');
                }
                if (!Schema::hasColumn('formations', 'uuid')) {
                    $table->uuid('uuid')->nullable()->unique()->after('id');
                }
            });

            // Backfill data for existing rows
            $formations = DB::table('formations')->select('id', 'title', 'slug', 'uuid')->get();
            foreach ($formations as $formation) {
                $updates = [];
                if (is_null($formation->uuid) || $formation->uuid === '') {
                    $updates['uuid'] = (string) Str::uuid();
                }
                if ((is_null($formation->slug) || $formation->slug === '') && !is_null($formation->title)) {
                    // Ensure uniqueness by appending an increment if needed
                    $base = Str::slug($formation->title);
                    $slug = $base;
                    $i = 1;
                    while (DB::table('formations')->where('slug', $slug)->where('id', '!=', $formation->id)->exists()) {
                        $slug = $base.'-'.(++$i);
                    }
                    $updates['slug'] = $slug;
                }
                if (!empty($updates)) {
                    DB::table('formations')->where('id', $formation->id)->update($updates);
                }
            }

            // Make columns non-nullable after backfill
            Schema::table('formations', function (Blueprint $table) {
                if (Schema::hasColumn('formations', 'slug')) {
                    // Do not call unique() here again to avoid duplicate index errors on some MySQL versions
                    $table->string('slug')->nullable(false)->change();
                }
                if (Schema::hasColumn('formations', 'uuid')) {
                    // Do not call unique() here again to avoid duplicate index errors on some MySQL versions
                    $table->uuid('uuid')->nullable(false)->change();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('formations')) {
            Schema::table('formations', function (Blueprint $table) {
                if (Schema::hasColumn('formations', 'slug')) {
                    $table->dropUnique(['slug']);
                    $table->dropColumn('slug');
                }
                if (Schema::hasColumn('formations', 'uuid')) {
                    $table->dropUnique(['uuid']);
                    $table->dropColumn('uuid');
                }
            });
        }
    }
};
