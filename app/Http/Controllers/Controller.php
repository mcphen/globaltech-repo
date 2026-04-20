<?php

namespace App\Http\Controllers;

use App\Models\Setting;

abstract class Controller
{
    /**
     * Provide centralized contact settings to front-end pages.
     * Controllers can call $this->getContactSettings() and pass it to Inertia.
     *
     * @return array
     */
    protected function getContactSettings(): array
    {
        return [
            'contact_phone' => Setting::get('contact_phone', '+225 07 00 00 00 00'),
            'contact_phone_fixed' => Setting::get('contact_phone_fixed', ''),
            'contact_email' => Setting::get('contact_email', 'contact@globaltech-edu.com'),
            'contact_address' => Setting::get('contact_address', 'Plateau, Abidjan, Côte d\'Ivoire'),
            'social_facebook' => Setting::get('social_facebook', 'https://www.facebook.com/globaltecheducationafrica/'),
            'social_twitter' => Setting::get('social_twitter', 'https://twitter.com/GlobalTECHEdu'),
            'social_instagram' => Setting::get('social_instagram', 'https://www.instagram.com/globaltecheducation/'),
            'social_youtube' => Setting::get('social_youtube', 'https://www.youtube.com/@globaltecheducationafrica'),
            'social_linkedin' => Setting::get('social_linkedin', 'https://www.linkedin.com/company/globaltech-education-africa/'),
            'social_tiktok' => Setting::get('social_tiktok', '#'),
            'opening_hours' => Setting::get('opening_hours', 'Lundi - Vendredi: 9h00 - 18h00'),
        ];
    }
}
