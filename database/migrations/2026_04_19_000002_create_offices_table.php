<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('city');
            $table->string('flag_emoji', 10)->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('type', ['headquarters', 'regional', 'representative'])->default('regional');
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed GlobalTECH offices
        DB::table('offices')->insert([
            ['country' => "Côte d'Ivoire", 'city' => 'Abidjan', 'flag_emoji' => '🇨🇮', 'address' => 'Plateau, Abidjan', 'type' => 'headquarters', 'sort_order' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'Sénégal', 'city' => 'Dakar', 'flag_emoji' => '🇸🇳', 'address' => 'Plateau, Dakar', 'type' => 'regional', 'sort_order' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'Mali', 'city' => 'Bamako', 'flag_emoji' => '🇲🇱', 'address' => 'ACI 2000, Bamako', 'type' => 'regional', 'sort_order' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'Cameroun', 'city' => 'Douala', 'flag_emoji' => '🇨🇲', 'address' => 'Bonanjo, Douala', 'type' => 'regional', 'sort_order' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'Burkina Faso', 'city' => 'Ouagadougou', 'flag_emoji' => '🇧🇫', 'address' => 'Ouaga 2000', 'type' => 'representative', 'sort_order' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['country' => 'Togo', 'city' => 'Lomé', 'flag_emoji' => '🇹🇬', 'address' => 'Centre-ville, Lomé', 'type' => 'representative', 'sort_order' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
