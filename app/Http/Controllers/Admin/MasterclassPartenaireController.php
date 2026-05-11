<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterclassPartenaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterclassPartenaireController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Masterclass/Partenaires/PartenaireIndex', [
            'partenaires' => MasterclassPartenaire::orderBy('sort_order')->get(),
            'flash'       => ['success' => session('success'), 'error' => session('error')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Masterclass/Partenaires/PartenaireForm');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:255',
            'logo_icon'   => 'nullable|string|max:100',
            'pays'        => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        MasterclassPartenaire::create($data);

        return redirect()->route('admin.masterclass.partenaires.index')
            ->with('success', 'Partenaire créé avec succès.');
    }

    public function edit(MasterclassPartenaire $partenaire)
    {
        return Inertia::render('Admin/Masterclass/Partenaires/PartenaireForm', [
            'partenaire' => $partenaire,
        ]);
    }

    public function update(Request $request, MasterclassPartenaire $partenaire)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:255',
            'logo_icon'   => 'nullable|string|max:100',
            'pays'        => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $partenaire->update($data);

        return redirect()->route('admin.masterclass.partenaires.index')
            ->with('success', 'Partenaire mis à jour.');
    }

    public function destroy(MasterclassPartenaire $partenaire)
    {
        $partenaire->delete();

        return redirect()->route('admin.masterclass.partenaires.index')
            ->with('success', 'Partenaire supprimé.');
    }
}
