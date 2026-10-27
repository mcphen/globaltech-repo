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
