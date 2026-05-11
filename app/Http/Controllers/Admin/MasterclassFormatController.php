<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterclassFormat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterclassFormatController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Masterclass/Formats/FormatIndex', [
            'formats' => MasterclassFormat::orderBy('sort_order')->get(),
            'flash'   => ['success' => session('success'), 'error' => session('error')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Masterclass/Formats/FormatForm');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon'        => 'nullable|string|max:100',
            'titre'       => 'required|string|max:255',
            'couleur'     => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string|max:100',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        MasterclassFormat::create($data);

        return redirect()->route('admin.masterclass.formats.index')
            ->with('success', 'Format créé avec succès.');
    }

    public function edit(MasterclassFormat $format)
    {
        return Inertia::render('Admin/Masterclass/Formats/FormatForm', [
            'format' => $format,
        ]);
    }

    public function update(Request $request, MasterclassFormat $format)
    {
        $data = $request->validate([
            'icon'        => 'nullable|string|max:100',
            'titre'       => 'required|string|max:255',
            'couleur'     => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string|max:100',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        $format->update($data);

        return redirect()->route('admin.masterclass.formats.index')
            ->with('success', 'Format mis à jour.');
    }

    public function destroy(MasterclassFormat $format)
    {
        $format->delete();

        return redirect()->route('admin.masterclass.formats.index')
            ->with('success', 'Format supprimé.');
    }
}
