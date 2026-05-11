<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterclassStat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterclassStatController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Masterclass/Stats/StatIndex', [
            'stats' => MasterclassStat::orderBy('sort_order')->get(),
            'flash' => ['success' => session('success'), 'error' => session('error')],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Masterclass/Stats/StatForm');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'valeur'     => 'required|string|max:50',
            'label'      => 'required|string|max:100',
            'sort_order' => 'integer|min:0',
        ]);

        MasterclassStat::create($data);

        return redirect()->route('admin.masterclass.stats.index')
            ->with('success', 'Statistique créée.');
    }

    public function edit(MasterclassStat $stat)
    {
        return Inertia::render('Admin/Masterclass/Stats/StatForm', [
            'stat' => $stat,
        ]);
    }

    public function update(Request $request, MasterclassStat $stat)
    {
        $data = $request->validate([
            'valeur'     => 'required|string|max:50',
            'label'      => 'required|string|max:100',
            'sort_order' => 'integer|min:0',
        ]);

        $stat->update($data);

        return redirect()->route('admin.masterclass.stats.index')
            ->with('success', 'Statistique mise à jour.');
    }

    public function destroy(MasterclassStat $stat)
    {
        $stat->delete();

        return redirect()->route('admin.masterclass.stats.index')
            ->with('success', 'Statistique supprimée.');
    }
}
