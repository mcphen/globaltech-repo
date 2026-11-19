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
