<?php

namespace App\Http\Controllers;

use App\Models\WhyUs;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WhyUsController extends Controller
{
    public function index()
    {
        $items = WhyUs::orderBy('sort_order')->orderBy('id')->paginate(20);
        return Inertia::render('Admin/WhyUs/WhyUsIndex', [
            'items' => $items,
            'flash' => ['success' => session('success')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/WhyUs/WhyUsCreate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon'       => 'required|string|max:100',
            'color'      => 'required|string|max:20',
            'title'      => 'required|string|max:255',
            'desc'       => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'active'     => 'boolean',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['active']     = $data['active'] ?? true;
        WhyUs::create($data);
        return redirect()->route('admin.why-us.index')
            ->with('success', 'Élément ajouté.');
    }

    public function edit(WhyUs $whyUs)
    {
        return Inertia::render('Admin/WhyUs/WhyUsEdit', [
            'item' => $whyUs,
        ]);
    }

    public function update(Request $request, WhyUs $whyUs)
    {
        $data = $request->validate([
            'icon'       => 'required|string|max:100',
            'color'      => 'required|string|max:20',
            'title'      => 'required|string|max:255',
            'desc'       => 'required|string',
            'sort_order' => 'nullable|integer|min:0',
            'active'     => 'boolean',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['active']     = isset($data['active']) ? $data['active'] : $whyUs->active;
        $whyUs->update($data);
        return redirect()->route('admin.why-us.index')
            ->with('success', 'Élément mis à jour.');
    }

    public function destroy(WhyUs $whyUs)
    {
        $whyUs->delete();
        return redirect()->route('admin.why-us.index')
            ->with('success', 'Élément supprimé.');
    }

    /**
     * Public API — returns active items ordered by sort_order
     */
    public function api()
    {
        return response()->json(
            WhyUs::where('active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(['id', 'icon', 'color', 'title', 'desc'])
        );
    }
}
