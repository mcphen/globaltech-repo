<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterclassPublicCible;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterclassPublicCibleController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Masterclass/PublicCibles/PublicCibleIndex', [
            'cibles' => MasterclassPublicCible::orderBy('sort_order')->get(),
            'flash'  => ['success' => session('success'), 'error' => session('error')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Masterclass/PublicCibles/PublicCibleForm');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon'        => 'nullable|string|max:100',
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        MasterclassPublicCible::create($data);

        return redirect()->route('admin.masterclass.public-cibles.index')
            ->with('success', 'Public cible créé avec succès.');
    }

    public function edit(MasterclassPublicCible $publicCible)
    {
        return Inertia::render('Admin/Masterclass/PublicCibles/PublicCibleForm', [
            'cible' => $publicCible,
        ]);
    }

    public function update(Request $request, MasterclassPublicCible $publicCible)
    {
        $data = $request->validate([
            'icon'        => 'nullable|string|max:100',
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $publicCible->update($data);

        return redirect()->route('admin.masterclass.public-cibles.index')
            ->with('success', 'Public cible mis à jour.');
    }

    public function destroy(MasterclassPublicCible $publicCible)
    {
        $publicCible->delete();

        return redirect()->route('admin.masterclass.public-cibles.index')
            ->with('success', 'Public cible supprimé.');
    }
}
