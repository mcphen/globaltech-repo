<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CartController extends Controller
{
    protected function getCart(): array
    {
        return session()->get('cart', []);
    }

    /**
     * Show the checkout page (requires authentication)
     */
    public function checkoutPage()
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        return Inertia::render('Front/Checkout', [
            'items' => array_values($cart),
            'totals' => $this->totals($cart),
            'contactSettings' => app(HomeController::class)->getContactSettings(),
        ]);
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
        $cart = $this->getCart();
        if (empty($cart)) {
            return back()->with('error', 'Votre panier est vide.');
        }

        // Optional customer info for guest checkout
        $data = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $totals = $this->totals($cart);

        $order = DB::transaction(function () use ($cart, $totals, $data) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'items_count' => $totals['count'],
                'subtotal' => $totals['subtotal'],
                'total' => $totals['total'],
                'currency' => 'XOF',
                'status' => 'pending',
                'customer_name' => $data['customer_name'] ?? null,
                'customer_email' => $data['customer_email'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cart as $item) {
                $qty = (int)($item["quantity"] ?? 1);
                $price = (float)($item["price"] ?? 0);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'] ?? null,
                    'title' => $item['title'] ?? 'Article',
                    'price' => $price,
                    'quantity' => $qty,
                    'line_total' => $price * $qty,
                    'data' => [
                        'image_url' => $item['image_url'] ?? null,
                    ],
                ]);
            }

            // Generate invoice metadata immediately after creation
            $order->invoice_number = $order->generateInvoiceNumber();
            $order->invoice_date = now();
            $order->save();

            return $order;
        });

        // Clear the cart after order creation
        $this->putCart([]);

        // After success, redirect to confirmation page
        session(['order_total' => (float)$order->total]);
        return redirect()->route('cart.confirmation');
    }

    /**
     * Show the order confirmation page after successful checkout
     */
    public function confirmation()
    {
        $total = session()->pull('order_total'); // read once and forget
        if ($total === null) {
            return redirect()->route('cart.index');
        }

        return Inertia::render('Front/CartConfirmationFront', [
            'total' => (float)$total,
            'contactSettings' => app(HomeController::class)->getContactSettings(),
        ]);
    }
}
