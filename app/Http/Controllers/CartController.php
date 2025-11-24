<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    protected function getCart(): array
    {
        return session()->get('cart', []);
    }

    protected function putCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    protected function totals(array $cart): array
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }
        $total = $subtotal; // No taxes/shipping for now
        return [
            'count' => array_sum(array_map(fn($i) => $i['quantity'] ?? 1, $cart)),
            'subtotal' => $subtotal,
            'total' => $total,
        ];
    }

    public function index()
    {
        $cart = $this->getCart();
        return Inertia::render('Front/CartFront', [
            'items' => array_values($cart),
            'totals' => $this->totals($cart),
            'contactSettings' => $this->getContactSettings(),
        ]);
    }

    public function add(Request $request, int $id)
    {
        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
        ]);
        $qty = (int)($validated['quantity'] ?? 1);

        $product = Product::findOrFail($id);
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $qty;
        } else {
            $cart[$id] = [
                'id' => $product->id,
                'title' => $product->title,
                'price' => $product->price,
                'image_url' => $product->image_path ? '/storage/' . $product->image_path : null,
                'quantity' => $qty,
            ];
        }
        $this->putCart($cart);

        return back()->with('success', 'Produit ajouté au panier.');
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $qty = (int)$validated['quantity'];
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $qty;
            $this->putCart($cart);
        }
        return back()->with('success', 'Panier mis à jour.');
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            unset($cart[$id]);
            $this->putCart($cart);
        }
        return back()->with('success', 'Produit retiré du panier.');
    }

    public function clear()
    {
        $this->putCart([]);
        return back()->with('success', 'Panier vidé.');
    }

    public function checkout(Request $request)
    {
        // For this simple session cart, we only clear and show a confirmation.
        $cart = $this->getCart();
        $totals = $this->totals($cart);
        $this->putCart([]);

        return Inertia::render('Front/CartConfirmationFront', [
            'total' => $totals['total'],
            'contactSettings' => app(HomeController::class)->getContactSettings(),
        ]);
    }
}
