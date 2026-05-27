<?php

namespace App\Http\Controllers;

use App\Models\ServicePage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServicePageController extends Controller
{
    public function edit()
    {
        return Inertia::render('Admin/ServicePage/Edit', [
            'servicePage' => ServicePage::first(),
        ]);
    }

    public function update(Request $request)
    {
        foreach (['hero_stats', 'consulting_offers', 'process_steps'] as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $request->merge([$field => json_decode($request->input($field), true) ?? []]);
            }
        }

        $data = $request->validate([
            'hero_badge'          => 'nullable|string|max:255',
            'hero_title'          => 'nullable|string|max:255',
            'hero_gradient_word'  => 'nullable|string|max:100',
            'hero_subtitle'       => 'nullable|string',
            'hero_stats'          => 'nullable|array',
            'hero_stats.*.n'      => 'nullable|string|max:20',
            'hero_stats.*.l'      => 'nullable|string|max:100',
            'hero_stats.*.icon'   => 'nullable|string|max:100',
            'services_label'      => 'nullable|string|max:255',
            'services_title'      => 'nullable|string|max:255',
            'consulting_label'    => 'nullable|string|max:255',
            'consulting_title'    => 'nullable|string|max:255',
            'consulting_subtitle' => 'nullable|string',
            'consulting_offers'   => 'nullable|array',
            'consulting_offers.*.icon'        => 'nullable|string|max:100',
            'consulting_offers.*.color'       => 'nullable|string|max:50',
            'consulting_offers.*.title'       => 'nullable|string|max:255',
            'consulting_offers.*.desc'        => 'nullable|string',
            'consulting_offers.*.deliverables' => 'nullable|array',
            'consulting_offers.*.deliverables.*' => 'nullable|string',
            'consulting_offers.*.featured'    => 'nullable|boolean',
            'process_label'       => 'nullable|string|max:255',
            'process_title'       => 'nullable|string|max:255',
            'process_steps'       => 'nullable|array',
            'process_steps.*.step' => 'nullable|string|max:5',
            'process_steps.*.icon' => 'nullable|string|max:100',
            'process_steps.*.title' => 'nullable|string|max:255',
            'process_steps.*.desc' => 'nullable|string',
            'cta_title'           => 'nullable|string|max:255',
            'cta_subtitle'        => 'nullable|string',
        ]);

        ServicePage::updateOrCreate([], $data);

        return redirect()->route('admin.service-page.edit')
            ->with('success', 'Page Consulting mise à jour.');
    }
}
