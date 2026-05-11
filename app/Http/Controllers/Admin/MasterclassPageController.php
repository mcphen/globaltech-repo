<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterclassPage;
use App\Models\MasterclassSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterclassPageController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/Masterclass/Page/PageEdit', [
            'page'    => MasterclassPage::firstOrCreate([]),
            'session' => MasterclassSession::firstOrCreate([]),
            'flash'   => ['success' => session('success'), 'error' => session('error')],
        ]);
    }

    public function updatePage(Request $request)
    {
        $data = $request->validate([
            'hero_badge'          => 'nullable|string|max:255',
            'hero_title'          => 'required|string|max:255',
            'hero_subtitle'       => 'nullable|string|max:255',
            'hero_description'    => 'nullable|string',
            'section_titre'       => 'nullable|string|max:255',
            'section_description' => 'nullable|string',
            'approche'            => 'nullable|string|max:100',
            'langues'             => 'nullable|string|max:100',
            'cohortes'            => 'nullable|string|max:100',
            'duree_info'          => 'nullable|string|max:100',
            'financement_info'    => 'nullable|string|max:255',
        ]);

        $page = MasterclassPage::firstOrCreate([]);
        $page->update($data);

        return redirect()->route('admin.masterclass.page.edit')
            ->with('success', 'Paramètres de la bannière mis à jour.');
    }

    public function updateSession(Request $request)
    {
        $data = $request->validate([
            'badge_text'       => 'nullable|string|max:255',
            'cta_titre'        => 'required|string|max:255',
            'cta_description'  => 'nullable|string',
            'date_session'     => 'nullable|string|max:100',
            'places_total'     => 'integer|min:1',
            'places_restantes' => 'integer|min:0',
            'prix_a_partir'    => 'nullable|string|max:100',
            'financement'      => 'nullable|string|max:255',
            'is_active'        => 'boolean',
        ]);

        $session = MasterclassSession::firstOrCreate([]);
        $session->update($data);

        return redirect()->route('admin.masterclass.page.edit')
            ->with('success', 'Section CTA mise à jour.');
    }
}
