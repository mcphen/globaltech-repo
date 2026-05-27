<?php

namespace App\Http\Controllers;

use App\Models\About;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    public function index()
    {
        $about = About::first();
        return Inertia::render('Front/About', [
            'about' => $about,
        ]);
    }

    public function indexAdminAbout()
    {
        $about = About::first();
        return Inertia::render('Admin/About/Index', [
            'about' => $about,
        ]);
    }

    public function edit()
    {
        $about = About::first();
        return Inertia::render('Admin/About/Edit', [
            'about' => $about,
        ]);
    }

    public function update(Request $request)
    {
        // Decode JSON array fields sent as strings from FormData
        foreach (['stats', 'values', 'milestones', 'offices'] as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $request->merge([$field => json_decode($request->input($field), true) ?? []]);
            }
        }

        $data = $request->validate([
            'content'          => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hero_badge'       => 'nullable|string|max:255',
            'hero_title'       => 'nullable|string|max:255',
            'hero_subtitle'    => 'nullable|string',
            'history_label'    => 'nullable|string|max:255',
            'history_title'    => 'nullable|string|max:255',
            'stats'            => 'nullable|array',
            'stats.*.value'    => 'nullable|string|max:50',
            'stats.*.label'    => 'nullable|string|max:100',
            'values'           => 'nullable|array',
            'values.*.icon'    => 'nullable|string|max:100',
            'values.*.color'   => 'nullable|string|max:50',
            'values.*.title'   => 'nullable|string|max:255',
            'values.*.desc'    => 'nullable|string',
            'timeline_label'   => 'nullable|string|max:255',
            'timeline_title'   => 'nullable|string|max:255',
            'milestones'       => 'nullable|array',
            'milestones.*.year'  => 'nullable|string|max:10',
            'milestones.*.event' => 'nullable|string',
            'offices_title'    => 'nullable|string|max:255',
            'offices_subtitle' => 'nullable|string',
            'offices'          => 'nullable|array',
            'offices.*.country' => 'nullable|string|max:100',
            'offices.*.city'    => 'nullable|string|max:100',
            'offices.*.flag'    => 'nullable|string|max:10',
            'offices.*.role'    => 'nullable|string|max:100',
            'offices.*.address' => 'nullable|string|max:255',
            'cta_title'        => 'nullable|string|max:255',
            'cta_subtitle'     => 'nullable|string',
        ]);

        $aboutData = collect($data)->except('image')->toArray();

        if ($request->hasFile('image')) {
            $about = About::first();
            if ($about?->getRawOriginal('image_path')) {
                Storage::disk('public')->delete($about->getRawOriginal('image_path'));
            }
            $aboutData['image_path'] = $request->file('image')->store('about', 'public');
        }

        About::updateOrCreate([], $aboutData);

        return redirect()->route('admin.about.index')
            ->with('success', 'Page À propos mise à jour.');
    }
}
