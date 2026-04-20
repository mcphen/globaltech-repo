<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private array $settings = [
        'site_name'         => 'GlobalTECH EDUCATION Africa',
        'site_tagline'      => 'Certifiez l\'Excellence de vos Équipes',
        'contact_email'     => 'contact@globaltech-edu.com',
        'contact_phone'     => '+225 07 00 00 00 00',
        'contact_phone_fixed' => '+225 27 00 00 00 00',
        'contact_address'   => 'Plateau, Abidjan, Côte d\'Ivoire',
        'social_facebook'   => 'https://www.facebook.com/globaltecheducationafrica/',
        'social_twitter'    => 'https://twitter.com/GlobalTECHEdu',
        'social_instagram'  => 'https://www.instagram.com/globaltecheducation/',
        'social_youtube'    => 'https://www.youtube.com/@globaltecheducationafrica',
        'social_linkedin'   => 'https://www.linkedin.com/company/globaltech-education-africa/',
        'social_tiktok'     => '#',
        'opening_hours'     => 'Lundi - Vendredi: 8h00 - 18h00',
    ];

    public function up(): void
    {
        foreach ($this->settings as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }
    }

    public function down(): void
    {
        // No rollback for brand settings — update manually if needed
    }
};
