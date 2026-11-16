<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Admin/Partners/PartnersIndex', [
            'partners' => $partners,
            'maxLogoMb' => self::maxLogoMb(),
            'flash' => ['success' => session('success')]
        ]);
    }

    public function getListeDatas(){
        $partners = Partner::query()->get();
        return response()->json($partners);
    }

    public function list()
    {
        $partners = Partner::orderBy('created_at', 'asc')
            ->get(['id', 'name', 'website_url', 'logo_path']);

        return response()->json($partners);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, true);
        $data['logo_path'] = $request->file('logo')->store('partners', 'public');
        Partner::create($data);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partenaire créé.');
    }

    public function update(Request $request, Partner $partner)
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('logo')) {
            $this->deleteLogo($partner);
            $data['logo_path'] = $request->file('logo')->store('partners', 'public');
        }

        $partner->update($data);

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partenaire mis à jour.');
    }

    /**
     * Taille max réelle d'un logo, en Mo : 4 Mo, ou moins si la configuration
     * PHP de l'hébergeur (upload_max_filesize / post_max_size) est plus basse.
     */
    public static function maxLogoMb(): float
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
            $toMb((string) ini_get('post_max_size')) - 0.5, // marge pour les autres champs
        ], fn ($l) => $l > 0);

        return round(min(4, ...$limits), 1);
    }

    /**
     * Logos : JPG, PNG, GIF, WebP ou SVG, 4 Mo max (ou la limite du serveur).
     * Les SVG contenant du script sont refusés.
     */
    private function validated(Request $request, bool $logoRequired): array
    {
        // Fichier rejeté par PHP avant Laravel (trop lourd pour l'hébergeur) :
        // message explicite plutôt que « Le logo est obligatoire ».
        $file = $request->files->get('logo');
        if ($file instanceof UploadedFile && ! $file->isValid()) {
            throw ValidationException::withMessages([
                'logo' => in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                    ? 'Fichier trop lourd : le serveur accepte au maximum '.self::maxLogoMb().' Mo par logo.'
                    : "L'envoi du logo a échoué. Réessayez.",
            ]);
        }

        $maxKb = (int) floor(self::maxLogoMb() * 1024);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'website_url' => 'nullable|url',
            'logo'        => [
                $logoRequired ? 'required' : 'nullable',
                'file',
                'mimes:jpg,jpeg,png,gif,webp,svg',
                'max:'.$maxKb,
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
            'name.required'   => 'Le nom est obligatoire.',
            'website_url.url' => 'Le site web doit être une adresse complète (https://…).',
            'logo.required'   => 'Le logo est obligatoire.',
            'logo.uploaded'   => "Le logo n'a pas pu être envoyé : fichier trop lourd pour le serveur.",
            'logo.mimes'      => 'Le logo doit être au format JPG, PNG, GIF, WebP ou SVG.',
            'logo.max'        => 'Fichier trop lourd : le logo ne doit pas dépasser '.self::maxLogoMb().' Mo.',
        ]);

        unset($data['logo']);

        return $data;
    }

    private function deleteLogo(Partner $partner): void
    {
        if ($partner->logo_path) {
            Storage::disk('public')->delete($partner->logo_path);
        }
    }

    public function destroy(Partner $partner)
    {
        $this->deleteLogo($partner);
        $partner->delete();

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partenaire supprimé.');
    }
}
