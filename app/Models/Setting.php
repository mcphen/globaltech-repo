<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label'
    ];

    /**
     * Get a setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return Setting
     */
    public static function set(string $key, $value)
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->save();

        return $setting;
    }

    /**
     * Get all settings by group
     *
     * @param string $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByGroup(string $group)
    {
        return self::where('group', $group)->get();
    }

    /**
     * Default presence countries shown in the front footer
     */
    public const DEFAULT_PRESENCE_COUNTRIES = [
        ['flag' => '🇨🇮', 'name' => "Côte d'Ivoire"],
        ['flag' => '🇸🇳', 'name' => 'Sénégal'],
        ['flag' => '🇲🇱', 'name' => 'Mali'],
        ['flag' => '🇨🇲', 'name' => 'Cameroun'],
        ['flag' => '🇧🇫', 'name' => 'Burkina'],
    ];

    /**
     * Default main menu (front header). Keys are fixed (each maps to a route
     * in LayoutFront.vue); only label, order and visibility are editable.
     */
    public const DEFAULT_NAV_MENU = [
        ['key' => 'home',        'label' => 'Accueil',               'visible' => true],
        ['key' => 'about',       'label' => 'À Propos',              'visible' => true],
        ['key' => 'formations',  'label' => 'Formations',            'visible' => true],
        ['key' => 'consulting',  'label' => 'Consulting',            'visible' => true],
        ['key' => 'masterclass', 'label' => 'Executive Masterclass', 'visible' => true],
        ['key' => 'blog',        'label' => 'Actualités',            'visible' => true],
        ['key' => 'contact',     'label' => 'Contact',               'visible' => true],
    ];

    /**
     * Stored menu merged with defaults: unknown keys dropped, missing keys
     * appended, empty labels replaced by the default label.
     */
    public static function navMenu(): array
    {
        $stored = json_decode((string) self::get('site_nav_menu', ''), true);
        $defaults = collect(self::DEFAULT_NAV_MENU)->keyBy('key');

        if (! is_array($stored)) {
            return self::DEFAULT_NAV_MENU;
        }

        $menu = [];
        foreach ($stored as $item) {
            $key = $item['key'] ?? null;
            if (! $defaults->has($key) || isset($menu[$key])) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? ''));
            $menu[$key] = [
                'key' => $key,
                'label' => $label !== '' ? $label : $defaults[$key]['label'],
                'visible' => (bool) ($item['visible'] ?? true),
            ];
        }
        foreach ($defaults as $key => $item) {
            $menu[$key] ??= $item;
        }

        return array_values($menu);
    }

    /**
     * Pages publiques dont la bannière (section du haut) peut recevoir une
     * image. Clé => libellé affiché dans l'admin.
     */
    public const PAGE_BANNERS = [
        'home'        => "Accueil (si aucun slide n'est actif)",
        'about'       => 'À Propos',
        'formations'  => 'Formations (liste et fiche formation)',
        'consulting'  => 'Consulting',
        'masterclass' => 'Executive Masterclass',
        'portfolio'   => 'Projets',
        'blog'        => 'Actualités (liste et article)',
        'contact'     => 'Contact',
    ];

    /**
     * Image de bannière de chaque page (chemin dans le disque public, ou null).
     */
    public static function pageBanners(): array
    {
        $stored = self::where('key', 'like', 'page_banner_%')->pluck('value', 'key');
        $banners = [];
        foreach (array_keys(self::PAGE_BANNERS) as $key) {
            $value = $stored['page_banner_'.$key] ?? null;
            $banners[$key] = $value !== '' ? $value : null;
        }

        return $banners;
    }

    /** Assombrissement par défaut de l'image de bannière, en % */
    public const PAGE_BANNER_DEFAULT_OVERLAY = 88;

    /**
     * Texte d'origine des bannières dont le texte est géré dans
     * Admin > Bannières des pages (les autres pages ont leur propre écran).
     */
    public const PAGE_BANNER_TEXT_DEFAULTS = [
        'formations' => [
            'badge' => 'Centre accrédité PMI — Pearson VUE',
            'title' => 'Formations',
            'highlight' => 'certifiantes',
            'subtitle' => 'Des programmes conçus par des experts pour vous mener à la certification internationale. IT, PMP, Management — choisissez votre filière.',
        ],
        'portfolio' => [
            'badge' => 'Portfolio & Réalisations',
            'title' => 'Nos projets',
            'highlight' => "à travers l'Afrique",
            'subtitle' => 'De Abidjan à Douala, en passant par Dakar et Bamako — découvrez les projets à impact que nous avons conduits pour nos clients.',
        ],
        'blog' => [
            'badge' => 'Actualités & Insights',
            'title' => 'Le blog',
            'highlight' => 'GlobalTECH',
            'subtitle' => 'Restez informé des dernières nouvelles, tendances et événements du monde de la formation professionnelle en Afrique.',
        ],
        'contact' => [
            'badge' => 'Parlons de votre projet',
            'title' => 'Contactez-nous',
            'highlight' => '',
            'subtitle' => 'Notre équipe est disponible pour répondre à toutes vos questions sur nos formations, consulting et projets.',
        ],
    ];

    /**
     * Réglages de bannière de chaque page : assombrissement (0-100) et,
     * pour les pages de PAGE_BANNER_TEXT_DEFAULTS, le texte (valeur
     * enregistrée, même vide, sinon texte d'origine).
     */
    public static function pageBannerMeta(): array
    {
        $stored = self::where('key', 'like', 'page_banner_%_meta')->pluck('value', 'key');
        $meta = [];
        foreach (array_keys(self::PAGE_BANNERS) as $key) {
            $saved = json_decode((string) ($stored['page_banner_'.$key.'_meta'] ?? ''), true);
            $saved = is_array($saved) ? $saved : [];

            $item = [
                'overlay' => isset($saved['overlay'])
                    ? max(0, min(100, (int) $saved['overlay']))
                    : self::PAGE_BANNER_DEFAULT_OVERLAY,
            ];
            if (isset(self::PAGE_BANNER_TEXT_DEFAULTS[$key])) {
                foreach (self::PAGE_BANNER_TEXT_DEFAULTS[$key] as $field => $default) {
                    $item[$field] = array_key_exists($field, $saved) ? (string) $saved[$field] : $default;
                }
            }
            $meta[$key] = $item;
        }

        return $meta;
    }

    /**
     * Logo et favicon du site : image envoyée dans Admin > Logo & favicon,
     * sinon les fichiers d'origine de public/images.
     */
    public const DEFAULT_LOGO = '/images/logo_globaltech.png';
    public const DEFAULT_FAVICON = '/images/favicon_globaltech.png';

    public static function branding(): array
    {
        $logo = self::get('site_logo');
        $favicon = self::get('site_favicon');

        return [
            'logo_url' => $logo ? '/storage/'.$logo : self::DEFAULT_LOGO,
            'favicon_url' => $favicon ? '/storage/'.$favicon : self::DEFAULT_FAVICON,
        ];
    }

    /**
     * Site-wide front settings (pages visibility, theme, presence)
     *
     * @return array
     */
    public static function siteSettings(): array
    {
        $countries = json_decode((string) self::get('site_presence_countries', ''), true);

        return [
            'contact_enabled' => self::get('site_contact_enabled', '1') === '1',
            'blog_enabled' => self::get('site_blog_enabled', '1') === '1',
            // 'user' = visitor chooses, 'dark' / 'light' = forced
            'theme_mode' => self::get('site_theme_mode', 'user'),
            'presence_countries' => is_array($countries) ? $countries : self::DEFAULT_PRESENCE_COUNTRIES,
            'nav_menu' => self::navMenu(),
            'page_banners' => self::pageBanners(),
            'page_banner_meta' => self::pageBannerMeta(),
            ...self::branding(),
        ];
    }

    /**
     * Get all settings as key-value pairs
     *
     * @return array
     */
    public static function getAllAsArray()
    {
        return self::all()->pluck('value', 'key')->toArray();
    }
}
