<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Services/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'icon'        => 'nullable|string|max:10',
            'subtitle'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'min_price'   => 'nullable|numeric|min:0',
            'items'       => 'nullable|array',
            'items.*.title' => 'required|string|max:255',
            'items.*.description' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('services', 'public');
        }

        // Create the service
        $service = Service::create([
            'title' => $data['title'],
            'icon' => $data['icon'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'description_old' => $data['description'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'min_price' => $data['min_price'] ?? null,
        ]);

        // Create service items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $index => $item) {
                $service->items()->create([
                    'title' => $item['title'],
                    'description' => $item['description'],
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Service créé.');

    }

    public function edit(Service $service)
    {
        // Load the service with its items
        $service->load('items');

        return Inertia::render('Admin/Services/Edit', [
            'service' => $service,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'icon'        => 'nullable|string|max:10',
            'subtitle'    => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'min_price'   => 'nullable|numeric|min:0',
            'items'       => 'nullable|array',
            'items.*.id'  => 'nullable|integer|exists:service_items,id',
            'items.*.title' => 'required|string|max:255',
            'items.*.description' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancien fichier
            if ($service->image_path) {
                Storage::disk('public')->delete($service->image_path);
            }
            $data['image_path'] = $request->file('image')->store('services', 'public');
        }

        // Update service data
        $service->update([
            'title' => $data['title'],
            'icon' => $data['icon'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'description_old' => $data['description'] ?? null,
            'min_price' => $data['min_price'] ?? null,
            'image_path' => $data['image_path'] ?? $service->image_path,
        ]);

        // Update service items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            // Get existing item IDs
            $existingItemIds = $service->items->pluck('id')->toArray();
            $updatedItemIds = [];

            foreach ($data['items'] as $index => $item) {
                if (isset($item['id'])) {
                    // Update existing item
                    $serviceItem = $service->items()->find($item['id']);
                    if ($serviceItem) {
                        $serviceItem->update([
                            'title' => $item['title'],
                            'description' => $item['description'],
                            'order' => $index,
                        ]);
                        $updatedItemIds[] = $item['id'];
                    }
                } else {
                    // Create new item
                    $newItem = $service->items()->create([
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'order' => $index,
                    ]);
                    $updatedItemIds[] = $newItem->id;
                }
            }

            // Delete items that were not updated or created
            $itemsToDelete = array_diff($existingItemIds, $updatedItemIds);
            if (!empty($itemsToDelete)) {
                $service->items()->whereIn('id', $itemsToDelete)->delete();
            }
        }

        return redirect()->route('admin.services.index')
            ->with('success', 'Service mis à jour.');


    }

    public function destroy(Service $service)
    {
        if ($service->image_path) {
            Storage::disk('public')->delete($service->image_path);
        }
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Service supprimé.');

    }

    /**
     * Get services for front-end display
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServices()
    {
        $services = Service::select('id', 'title', 'description_old', 'image_path', 'min_price')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title,
                    'description' => $service->description_old,
                    'image_url' => $service->image_path ? '/storage/' . $service->image_path : null,
                    'min_price' => $service->min_price,
                ];
            });

        return response()->json($services);
    }

    /**
     * Get all services with their items for the services page
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllServicesWithItems()
    {
        $services = Service::with('items')
            ->orderBy('id')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'icon' => $service->icon,
                    'title' => $service->title,
                    'subtitle' => $service->subtitle,
                    'items' => $service->items->map(function ($item) {
                        return [
                            'title' => $item->title,
                            'description' => $item->description,
                        ];
                    }),
                ];
            });

        return response()->json($services);
    }
}
