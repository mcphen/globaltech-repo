<?php

namespace App\Http\Controllers;

use App\Models\HomePage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomePageController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/HomePage/Edit', [
            'homePage' => HomePage::first(),
        ]);
    }

    public function update(Request $request)
    {
        // Decode JSON array fields sent as strings from FormData
        foreach (['hero_trust_badges', 'hero_mini_stats', 'stats', 'b2b_list_items', 'b2b_benefits'] as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $request->merge([$field => json_decode($request->input($field), true) ?? []]);
            }
        }

        $data = $request->validate([
            'hero_badge'        => 'nullable|string|max:255',
            'hero_title_1'      => 'nullable|string|max:255',
            'hero_title_2'      => 'nullable|string|max:255',
            'hero_title_3'      => 'nullable|string|max:255',
            'hero_subtitle'     => 'nullable|string',
            'hero_trust_badges' => 'nullable|array',
            'hero_trust_badges.*' => 'nullable|string|max:50',
            'hero_mini_stats'   => 'nullable|array',
            'hero_mini_stats.*.v' => 'nullable|string|max:20',
            'hero_mini_stats.*.l' => 'nullable|string|max:50',
            'stats'             => 'nullable|array',
            'stats.*.value'     => 'nullable|integer',
            'stats.*.suffix'    => 'nullable|string|max:10',
            'stats.*.label'     => 'nullable|string|max:100',
            'stats.*.icon'      => 'nullable|string|max:100',
            'pillars_label'     => 'nullable|string|max:255',
            'pillars_title'     => 'nullable|string|max:255',
            'pillars_subtitle'  => 'nullable|string',
            'b2b_badge'         => 'nullable|string|max:255',
            'b2b_title_1'       => 'nullable|string|max:255',
            'b2b_title_2'       => 'nullable|string|max:255',
            'b2b_subtitle'      => 'nullable|string',
            'b2b_list_items'    => 'nullable|array',
            'b2b_list_items.*'  => 'nullable|string',
            'b2b_benefits'      => 'nullable|array',
            'b2b_benefits.*.icon'  => 'nullable|string|max:100',
            'b2b_benefits.*.title' => 'nullable|string|max:255',
            'b2b_benefits.*.desc'  => 'nullable|string',
            'why_label'         => 'nullable|string|max:255',
            'why_title'         => 'nullable|string|max:255',
            'why_subtitle'      => 'nullable|string',
            'portfolio_label'   => 'nullable|string|max:255',
            'portfolio_title'   => 'nullable|string|max:255',
            'testimonials_label' => 'nullable|string|max:255',
            'testimonials_title' => 'nullable|string|max:255',
            'news_label'        => 'nullable|string|max:255',
            'news_title'        => 'nullable|string|max:255',
            'cta_badge'         => 'nullable|string|max:255',
            'cta_title'         => 'nullable|string|max:255',
            'cta_gradient_word' => 'nullable|string|max:100',
            'cta_subtitle'      => 'nullable|string',
        ]);

        HomePage::updateOrCreate([], $data);

        return redirect()->route('admin.home-page.edit')
            ->with('success', 'Page d\'accueil mise à jour.');
    }
}
