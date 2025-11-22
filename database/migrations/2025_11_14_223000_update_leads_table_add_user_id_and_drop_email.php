<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Add the user reference (nullable to allow set null on user deletion)
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // Ensure 1:1 mapping between a user (role=lead) and a lead profile
            $table->unique('user_id');

            // Drop the email column and its index, since email will live on users
            $table->dropIndex(['email']);
            $table->dropColumn('email');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Restore the email column (as originally defined) and its index
            $table->string('email')->after('last_name');
            $table->index(['email']);

            // Remove the user linkage
            $table->dropUnique(['user_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
