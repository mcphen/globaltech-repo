<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image de fond de la bannière (section du haut) de chaque page publique.
 * Sans image, la page garde le dégradé bleu par défaut.
 */
class PageBannerController extends Controller
{
    public function index()
    {
        $banners = Setting::pageBanners();

        return Inertia::render('Admin/PageBanners/Index', [
            'pages' => collect(Setting::PAGE_BANNERS)
                ->map(fn ($label, $key) => [
                    'key' => $key,
                    'label' => $label,
                    'image_path' => $banners[$key],
                ])
                ->values(),
            'maxMb' => self::maxMb(),
            'flash' => ['success' => session('success')],
        ]);
    }

    public function update(Request $request, string $page)
    {
        abort_unless(array_key_exists($page, Setting::PAGE_BANNERS), 404);

        $file = $request->files->get('image');
        if ($file instanceof UploadedFile && ! $file->isValid()) {
            throw ValidationException::withMessages([
                'image' => in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                    ? 'Fichier trop lourd : le serveur accepte au maximum '.self::maxMb().' Mo par image.'
                    : "L'envoi de l'image a échoué. Réessayez.",
            ]);
        }

        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:'.(int) floor(self::maxMb() * 1024),
        ], [
            'image.required' => 'Choisissez une image.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => "L'image doit être au format JPG, PNG ou WebP.",
            'image.max' => "Fichier trop lourd : l'image ne doit pas dépasser ".self::maxMb().' Mo.',
        ]);

        $this->deleteImage($page);
        Setting::set('page_banner_'.$page, $request->file('image')->store('page-banners', 'public'));

        return redirect()->back()->with('success', 'Bannière mise à jour.');
    }

    public function destroy(string $page)
    {
        abort_unless(array_key_exists($page, Setting::PAGE_BANNERS), 404);

        $this->deleteImage($page);
        Setting::set('page_banner_'.$page, '');

        return redirect()->back()->with('success', 'Bannière supprimée : la page reprend le fond par défaut.');
    }

    private function deleteImage(string $page): void
    {
        $path = Setting::get('page_banner_'.$page);
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    /** Taille max d'une image en Mo : 5 Mo, ou moins si la config PHP est plus basse. */
    private static function maxMb(): float
    {
        $toMb = function (string $v): float {
            $v = trim($v);
            $n = (float) $v;

            return match (strtolower(substr($v, -1))) {
                'g' => $n * 1024,
                'm' => $n,
                'k' => $n / 1024,
                default => $n / 1024 / 1024,
            };
        };
        $limits = array_filter([
            $toMb((string) ini_get('upload_max_filesize')),
            $toMb((string) ini_get('post_max_size')) - 0.5,
        ], fn ($l) => $l > 0);

        return round(min(5, ...$limits), 1);
    }
}
