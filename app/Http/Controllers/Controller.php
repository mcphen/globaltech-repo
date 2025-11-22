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
            'contact_phone' => Setting::get('contact_phone', '+(33) 7 52 90 27 63'),
            'contact_phone_fixed' => Setting::get('contact_phone_fixed', ''),
            'contact_email' => Setting::get('contact_email', 'contact@ngconsultingpro.com'),
            'contact_address' => Setting::get('contact_address', '71 rue de fontenay, 92140 ville Clamart, France'),
            'social_facebook' => Setting::get('social_facebook', 'https://www.facebook.com/ngconsulting/'),
            'social_twitter' => Setting::get('social_twitter', 'https://twitter.com/ngconsulting'),
            'social_instagram' => Setting::get('social_instagram', 'https://www.instagram.com/ngconsulting/'),
            'social_youtube' => Setting::get('social_youtube', 'https://www.youtube.com/ngconsulting'),
            'social_linkedin' => Setting::get('social_linkedin', 'https://www.linkedin.com/company/ngconsulting/'),
            'social_tiktok' => Setting::get('social_tiktok', '#'),
            'opening_hours' => Setting::get('opening_hours', 'Lundi - Vendredi: 9h00 - 18h00'),
        ];
    }
}
