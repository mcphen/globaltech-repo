<?php

namespace App\Http\Controllers;

use App\Models\LeadFormation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadFormationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Ensure lead exists
        $lead = $user->loadMissing('lead')->lead;
        if (!$lead) {
            return response()->json([]);
        }

        $orders = LeadFormation::with(['formation:id,title,price,currency'])
            ->where('lead_id', $lead->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (LeadFormation $lf) {
                return [
                    'id' => $lf->id,
                    'reference' => 'LF-'.$lf->id,
                    'created_at' => $lf->created_at?->toDateTimeString(),
                    'total' => $lf->formation?->price,
                    'amount' => $lf->formation?->price, // fallback key used by UI
                    'currency' => $lf->formation?->currency ?? 'FCFA',
                    'status' => $lf->status ?? 'unpaid',
                    'formation' => [
                        'id' => $lf->formation?->id,
                        'title' => $lf->formation?->title,
                    ],
                ];
            });

        return response()->json($orders);
    }
}
