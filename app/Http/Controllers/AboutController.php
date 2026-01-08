<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    // Affiche la page About via Inertia
    public function index()
    {
        $about = About::first();
        return Inertia::render('About', [
            'about' => $about
        ]);
    }

    public function indexAdminAbout()
    {
        $about = About::first();
        return Inertia::render('Admin/About/Index', [
            'about' => $about
        ]);
    }

    // Affiche la page d'édition en back-office
    public function edit()
    {
        $about = About::first();
        return Inertia::render('Admin/About/Edit', [
            'about' => $about
        ]);
    }

    // Mise à jour du contenu depuis le BO
    public function update(Request $request)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $aboutData = ['content' => $data['content']];

        // Traitement de l'image si elle est présente
        if ($request->hasFile('image')) {
            $about = About::first();

            // Suppression de l'ancienne image si elle existe
            if ($about && $about->image_path) {
                Storage::disk('public')->delete($about->image_path);
            }

            // Stockage de la nouvelle image
            $path = $request->file('image')->store('about', 'public');
            $aboutData['image_path'] = $path;
        }

        About::updateOrCreate([], $aboutData);

        $about = About::first();
        return Inertia::render('Admin/About/Edit', [
            'about' => $about,
            'flash' => ['success' => 'Contenu mis à jour.'],
        ]);
    }
}
