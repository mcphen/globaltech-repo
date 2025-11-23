<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add columns only if they don't already exist
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('leads', 'company')) {
                $table->string('company')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('leads', 'consent_at')) {
                $table->timestamp('consent_at')->nullable()->after('company');
            }
        });

        // Add the foreign key if possible; ignore if it already exists
        if (Schema::hasColumn('leads', 'user_id')) {
            try {
                Schema::table('leads', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                });
            } catch (\Throwable $e) {
                // Likely the foreign key already exists; ignore to keep migration idempotent
            }
        }
    }

    public function down(): void
    {
        // Drop foreign key and columns only if they exist
        if (Schema::hasColumn('leads', 'user_id')) {
            try {
                Schema::table('leads', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                });
            } catch (\Throwable $e) {
                // Foreign key may not exist; ignore
            }
        }

        Schema::table('leads', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('leads', 'user_id')) {
                $columnsToDrop[] = 'user_id';
            }
            if (Schema::hasColumn('leads', 'company')) {
                $columnsToDrop[] = 'company';
            }
            if (Schema::hasColumn('leads', 'consent_at')) {
                $columnsToDrop[] = 'consent_at';
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
