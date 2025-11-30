<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        // Render the Inertia page; data will be loaded via axios from list() endpoint
        return Inertia::render('Admin/Orders/OrderIndex');
    }

    public function list(Request $request)
    {
        $query = Order::query()->latest('id');

        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $perPage = (int)($request->get('per_page', 10));
        $orders = $query->paginate($perPage)->withQueryString();

        return response()->json($orders);
    }

    public function show(Order $order)
    {
        $order->load(['items', 'user']);
        return Inertia::render('Admin/Orders/OrderShow', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,paid,canceled',
        ]);

        $order->update(['status' => $data['status']]);

        return response()->json([
            'message' => 'Statut mis à jour avec succès',
            'order' => $order->fresh(),
        ]);
    }
}
