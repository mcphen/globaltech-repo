<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Add nullable user_id to link the lead with created user
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->string('company')->nullable()->after('phone');
            $table->timestamp('consent_at')->nullable()->after('company');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'company', 'consent_at']);
        });
    }
};
