<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterclassCertification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterclassCertificationController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Masterclass/Certifications/CertificationIndex', [
            'certifications' => MasterclassCertification::orderBy('sort_order')->get(),
            'flash'          => ['success' => session('success'), 'error' => session('error')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Masterclass/Certifications/CertificationForm');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'badge_icon'  => 'nullable|string|max:100',
            'couleur'     => 'nullable|string|max:20',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        MasterclassCertification::create($data);

        return redirect()->route('admin.masterclass.certifications.index')
            ->with('success', 'Certification créée avec succès.');
    }

    public function edit(MasterclassCertification $certification)
    {
        return Inertia::render('Admin/Masterclass/Certifications/CertificationForm', [
            'certification' => $certification,
        ]);
    }

    public function update(Request $request, MasterclassCertification $certification)
    {
        $data = $request->validate([
            'titre'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'badge_icon'  => 'nullable|string|max:100',
            'couleur'     => 'nullable|string|max:20',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $certification->update($data);

        return redirect()->route('admin.masterclass.certifications.index')
            ->with('success', 'Certification mise à jour.');
    }

    public function destroy(MasterclassCertification $certification)
    {
        $certification->delete();

        return redirect()->route('admin.masterclass.certifications.index')
            ->with('success', 'Certification supprimée.');
    }
}
