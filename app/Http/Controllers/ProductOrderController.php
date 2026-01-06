<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class ProductOrderController extends Controller
{
    /**
     * Return the authenticated user's product orders as JSON.
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $orders = Order::query()
            ->where('user_id', $userId)
            ->latest('id')
            ->get([
                'id',
                'created_at',
                'total',
                'currency',
                'status',
                'items_count',
            ]);

        return response()->json($orders);
    }
}