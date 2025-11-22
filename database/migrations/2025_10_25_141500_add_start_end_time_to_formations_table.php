<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('formations')) {
            Schema::table('formations', function (Blueprint $table) {
                if (!Schema::hasColumn('formations', 'start_time')) {
                    $table->time('start_time')->nullable()->after('date');
                }
                if (!Schema::hasColumn('formations', 'end_time')) {
                    $table->time('end_time')->nullable()->after('start_time');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('formations')) {
            Schema::table('formations', function (Blueprint $table) {
                if (Schema::hasColumn('formations', 'start_time')) {
                    $table->dropColumn('start_time');
                }
                if (Schema::hasColumn('formations', 'end_time')) {
                    $table->dropColumn('end_time');
                }
            });
        }
    }
};
