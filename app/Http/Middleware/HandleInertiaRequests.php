<?php

namespace App\Http\Middleware;

use App\Models\FormationCategory;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        // Build shared cart data from session
        $cart = session()->get('cart', []);
        $subtotal = 0;
        $quantityCount = 0; // total quantity across all items
        foreach ($cart as $item) {
            $qty = (int)($item['quantity'] ?? 1);
            $price = (float)($item['price'] ?? 0);
            $subtotal += $qty * $price;
            $quantityCount += $qty;
        }
        // Distinct item count (number of different products in cart)
        $distinctCount = count($cart);
        $total = $subtotal; // No extra fees for now

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'formationCategories' => fn () => rescue(
                fn () => FormationCategory::orderBy('sort_order')
                    ->get(['id', 'name', 'slug', 'icon', 'color', 'is_featured']),
                collect([])
            ),
            'cart' => [
                'items' => array_values($cart),
                'count' => $distinctCount, // show number of distinct items in UI
                'subtotal' => $subtotal,
                'total' => $total,
            ],
        ];
    }
}
