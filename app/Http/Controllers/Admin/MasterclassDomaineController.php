<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterclassDomaine;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterclassDomaineController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Masterclass/Domaines/DomaineIndex', [
            'domaines' => MasterclassDomaine::orderBy('sort_order')->get(),
            'flash'    => ['success' => session('success'), 'error' => session('error')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Masterclass/Domaines/DomaineForm');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'niveau'      => 'nullable|string|max:100',
            'duree'       => 'nullable|string|max:100',
            'modules'     => 'nullable|array',
            'modules.*'   => 'string|max:255',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        MasterclassDomaine::create($data);

        return redirect()->route('admin.masterclass.domaines.index')
            ->with('success', 'Domaine créé avec succès.');
    }

    public function edit(MasterclassDomaine $domaine)
    {
        return Inertia::render('Admin/Masterclass/Domaines/DomaineForm', [
            'domaine' => $domaine,
        ]);
    }

    public function update(Request $request, MasterclassDomaine $domaine)
    {
        $data = $request->validate([
            'icon'        => 'nullable|string|max:100',
            'color'       => 'nullable|string|max:20',
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'niveau'      => 'nullable|string|max:100',
            'duree'       => 'nullable|string|max:100',
            'modules'     => 'nullable|array',
            'modules.*'   => 'string|max:255',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $domaine->update($data);

        return redirect()->route('admin.masterclass.domaines.index')
            ->with('success', 'Domaine mis à jour avec succès.');
    }

    public function destroy(MasterclassDomaine $domaine)
    {
        $domaine->delete();

        return redirect()->route('admin.masterclass.domaines.index')
            ->with('success', 'Domaine supprimé.');
    }
}
