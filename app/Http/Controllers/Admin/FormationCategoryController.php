<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class FormationCategoryController extends Controller
{
    public function index()
    {
        $categories = FormationCategory::withCount('formations')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Admin/FormationCategories/FormationCategoryIndex', [
            'categories' => $categories,
            'flash' => [
                'success' => session('success'),
                'error'   => session('error'),
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/FormationCategories/FormationCategoryCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'label'       => 'nullable|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:formation_categories,slug',
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'background'  => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
            'sort_order'  => 'integer|min:0',
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        FormationCategory::create($data);

        return redirect()->route('admin.formation-categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(FormationCategory $formationCategory)
    {
        return Inertia::render('Admin/FormationCategories/FormationCategoryEdit', [
            'category' => $formationCategory,
        ]);
    }

    public function update(Request $request, FormationCategory $formationCategory)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'label'       => 'nullable|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:formation_categories,slug,' . $formationCategory->id,
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'background'  => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_featured' => 'boolean',
            'sort_order'  => 'integer|min:0',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $formationCategory->update($data);

        return redirect()->route('admin.formation-categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(FormationCategory $formationCategory)
    {
        if ($formationCategory->formations()->count() > 0) {
            return redirect()->route('admin.formation-categories.index')
                ->with('error', 'Impossible de supprimer : cette catégorie contient des formations.');
        }

        $formationCategory->delete();

        return redirect()->route('admin.formation-categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}
