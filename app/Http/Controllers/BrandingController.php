<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Logo et favicon du site, modifiables depuis l'admin.
 * Sans image envoyée, le site utilise les fichiers d'origine (public/images).
 */
class BrandingController extends Controller
{
    /** type => [clé du réglage, formats acceptés, libellé] */
    private const TYPES = [
        'logo' => ['site_logo', 'jpg,jpeg,png,webp,svg', 'Le logo'],
        'favicon' => ['site_favicon', 'png,ico,jpg,jpeg,webp,svg', 'Le favicon'],
    ];

    public function index()
    {
        return Inertia::render('Admin/Branding/Index', [
            'branding' => [
                ...Setting::branding(),
                'logo_custom' => (bool) Setting::get('site_logo'),
                'favicon_custom' => (bool) Setting::get('site_favicon'),
            ],
            'maxMb' => PartnerController::maxLogoMb(),
            'flash' => ['success' => session('success')],
        ]);
    }

    public function update(Request $request, string $type)
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        [$key, $mimes, $label] = self::TYPES[$type];
        $maxMb = PartnerController::maxLogoMb();

        $file = $request->files->get('image');
        if ($file instanceof UploadedFile && ! $file->isValid()) {
            throw ValidationException::withMessages([
                'image' => in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                    ? "Fichier trop lourd : le serveur accepte au maximum {$maxMb} Mo."
                    : "L'envoi du fichier a échoué. Réessayez.",
            ]);
        }

        $request->validate([
            'image' => [
                'required',
                'file',
                'mimes:'.$mimes,
                'max:'.(int) floor($maxMb * 1024),
                function ($attribute, $value, $fail) {
                    if (strtolower($value->getClientOriginalExtension()) !== 'svg') {
                        return;
                    }
                    $svg = (string) file_get_contents($value->getRealPath());
                    if (preg_match('/<script|\son[a-z]+\s*=|javascript:/i', $svg)) {
                        $fail('Ce fichier SVG contient du code et ne peut pas être utilisé.');
                    }
                },
            ],
        ], [
            'image.required' => 'Choisissez un fichier.',
            'image.mimes' => "{$label} doit être au format ".strtoupper(str_replace(',', ', ', $mimes)).'.',
            'image.max' => "Fichier trop lourd : {$maxMb} Mo maximum.",
        ]);

        $this->deleteFile($key);
        Setting::set($key, $request->file('image')->store('branding', 'public'));

        return redirect()->back()->with('success', "{$label} a été mis à jour.");
    }

    public function destroy(string $type)
    {
        abort_unless(isset(self::TYPES[$type]), 404);
        [$key, , $label] = self::TYPES[$type];

        $this->deleteFile($key);
        Setting::set($key, '');

        return redirect()->back()->with('success', "{$label} d'origine a été rétabli.");
    }

    private function deleteFile(string $key): void
    {
        $path = Setting::get($key);
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
