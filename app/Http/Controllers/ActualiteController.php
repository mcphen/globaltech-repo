<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ActualiteController extends Controller
{
    public function index()
    {
        $actualites = Actualite::orderBy('published_at', 'desc')->paginate(10);
        return Inertia::render('Admin/Actualites/ActualiteIndex', [
            'actualites' => $actualites,
            'flash' => ['success' => session('success')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Actualites/ActualitesCreate', [
            'categories' => Actualite::CATEGORIES,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'published_at' => 'required|date',
            'category'     => 'nullable|string|in:' . implode(',', array_keys(Actualite::CATEGORIES)),
        ]);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('actualites', 'public');
        }
        $data['category'] = $data['category'] ?? 'actualite';
        Actualite::create($data);
        return redirect()->route('admin.actualites.index')
            ->with('success', 'Actualité créée.');
    }

    public function edit(Actualite $actualite)
    {
        return Inertia::render('Admin/Actualites/ActualitesEdit', [
            'actualite'  => $actualite,
            'categories' => Actualite::CATEGORIES,
        ]);
    }

    public function update(Request $request, Actualite $actualite)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'image'        => 'nullable|image|max:2048',
            'published_at' => 'required|date',
            'category'     => 'nullable|string|in:' . implode(',', array_keys(Actualite::CATEGORIES)),
        ]);
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($actualite->image_path);
            $data['image_path'] = $request->file('image')->store('actualites', 'public');
        }
        $data['category'] = $data['category'] ?? 'actualite';
        $actualite->update($data);
        return redirect()->route('admin.actualites.index')
            ->with('success', 'Actualité mise à jour.');
    }

    public function destroy(Actualite $actualite)
    {
        Storage::disk('public')->delete($actualite->image_path);
        $actualite->delete();
        return redirect()->route('admin.actualites.index')
            ->with('success', 'Actualité supprimée.');
    }

    /**
     * Get the latest published actualites
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function latest()
    {
        $actualites = Actualite::query()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('category', '!=', 'projet')
            ->orderBy('published_at', 'desc')
            ->take(3)
            ->get();

        return response()->json($actualites);
    }

    /**
     * Get published actualites with category 'projet' for the portfolio section.
     */
    public function projects()
    {
        $projects = Actualite::query()
            ->where('category', 'projet')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->get()
            ->map(fn ($a) => [
                'id'          => $a->id,
                'title'       => $a->title,
                'description' => $a->description,
                'image_url'   => $a->image_url,
                'published_at'=> $a->published_at,
            ]);

        return response()->json($projects);
    }
}
