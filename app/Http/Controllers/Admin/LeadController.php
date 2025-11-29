<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lead;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\Formation;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeadController extends Controller
{
    /**
     * Display a listing of the leads.
     */
    // public function index()
    // {
    //     $leads = Lead::with(['user:id,name,email,created_at', 'formations'])
    //         ->orderByDesc('created_at')
    //         ->get()
    //         ->map(function ($lead) {
    //             return [
    //                 'id' => $lead->id,
    //                 'first_name' => $lead->first_name,
    //                 'last_name' => $lead->last_name,
    //                 'full_name' => $lead->first_name . ' ' . $lead->last_name,
    //                 'phone' => $lead->phone,
    //                 'company' => $lead->company,
    //                 'email' => $lead->user->email ?? 'N/A',
    //                 'consent_at' => $lead->consent_at?->toDateTimeString(),
    //                 'created_at' => $lead->created_at->toDateTimeString(),
    //                 'updated_at' => $lead->updated_at->toDateTimeString(),
    //                 'formations_count' => $lead->formations->count(),
    //                 'user' => $lead->user ? [
    //                     'id' => $lead->user->id,
    //                     'name' => $lead->user->name,
    //                     'email' => $lead->user->email,
    //                     'created_at' => $lead->user->created_at->toDateTimeString(),
    //                 ] : null,
    //             ];
    //         });

    //     return Inertia::render('Admin/Leads/LeadIndex', [
    //         'leads' => $leads
    //     ]);
    // }

// Dans Admin/LeadController.php - méthode index()
public function index()
{
    // Récupérer tous les leads avec leurs relations
    $leads = Lead::with(['user', 'formations', 'appointments'])
        ->withCount(['formations', 'appointments'])
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($lead) {
            return [
                'id' => $lead->id,
                'first_name' => $lead->first_name,
                'last_name' => $lead->last_name,
                'full_name' => $lead->first_name . ' ' . $lead->last_name,
                'phone' => $lead->phone,
                'company' => $lead->company,
                'email' => $lead->user->email ?? 'N/A',
                'consent_at' => $lead->consent_at?->toDateTimeString(),
                'created_at' => $lead->created_at->toDateTimeString(),
                'updated_at' => $lead->updated_at->toDateTimeString(),
                'formations_count' => $lead->formations_count,
                'appointments_count' => $lead->appointments_count,
                'user' => $lead->user ? [
                    'id' => $lead->user->id,
                    'name' => $lead->user->name,
                    'email' => $lead->user->email,
                    'created_at' => $lead->user->created_at->toDateTimeString(),
                ] : null,
            ];
        });

    // Statistiques globales
    $totalLeads = Lead::count();
    $totalWithConsent = Lead::whereNotNull('consent_at')->count();
    $totalWithFormations = Lead::has('formations')->count();
    $totalWithAppointments = Lead::has('appointments')->count();

    return Inertia::render('Admin/Leads/LeadIndex', [
        'leads' => $leads,
        'stats' => [
            'total_leads' => $totalLeads,
            'with_consent' => $totalWithConsent,
            'with_formations' => $totalWithFormations,
            'with_appointments' => $totalWithAppointments,
        ]
    ]);
}


    /**
     * Display the specified lead with all related data.
     */
    public function show(Lead $lead)
    {
        // Charger toutes les données liées au lead
        $lead->load([
            'user',
            'formations' => function ($query) {
                $query->withPivot('attentes', 'status', 'paid_at', 'created_at');
            },
            'user.orders' => function ($query) {
                $query->with('items')->orderByDesc('created_at');
            },
            'user.orders.items',
            'appointments' => function ($query) {
                $query->with(['schedule', 'services'])->orderByDesc('created_at');
            }
        ]);

        // Calcul des statistiques
        $stats = $this->getLeadStats($lead);

        $leadData = [
            'id' => $lead->id,
            'first_name' => $lead->first_name,
            'last_name' => $lead->last_name,
            'full_name' => $lead->first_name . ' ' . $lead->last_name,
            'phone' => $lead->phone,
            'company' => $lead->company,
            'email' => $lead->user->email ?? 'N/A',
            'consent_at' => $lead->consent_at?->toDateTimeString(),
            'created_at' => $lead->created_at->toDateTimeString(),
            'updated_at' => $lead->updated_at->toDateTimeString(),
            'user' => $lead->user ? [
                'id' => $lead->user->id,
                'name' => $lead->user->name,
                'email' => $lead->user->email,
                'email_verified_at' => $lead->user->email_verified_at?->toDateTimeString(),
                'created_at' => $lead->user->created_at->toDateTimeString(),
            ] : null,
            'formations' => $lead->formations->map(function ($formation) use ($lead) {
                $pivot = $formation->pivot;
                return [
                    'id' => $formation->id,
                    'title' => $formation->title,
                    'price' => $formation->price,
                    'currency' => $formation->currency,
                    'duration_hours' => $formation->duration_hours,
                    'pivot' => [
                        'id' => $pivot->id,
                        'attentes' => $pivot->attentes,
                        'status' => $pivot->status,
                        'paid_at' => $pivot->paid_at?->toDateTimeString(),
                        'created_at' => $pivot->created_at?->toDateTimeString(),
                    ]
                ];
            }),
            'orders' => $lead->user ? $lead->user->orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference' => 'CMD-' . $order->id,
                    'items_count' => $order->items_count,
                    'subtotal' => $order->subtotal,
                    'total' => $order->total,
                    'currency' => $order->currency,
                    'status' => $order->status,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'customer_phone' => $order->customer_phone,
                    'notes' => $order->notes,
                    'created_at' => $order->created_at->toDateTimeString(),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'price' => $item->price,
                            'quantity' => $item->quantity,
                            'line_total' => $item->line_total,
                        ];
                    })
                ];
            }) : [],
            'appointments' => $lead->appointments->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'subject' => $appointment->subject,
                    'description' => $appointment->description,
                    'status' => $appointment->status,
                    'schedule' => $appointment->schedule ? [
                        'date' => $appointment->schedule->date?->toDateString(),
                        'start_time' => $appointment->schedule->start_time,
                        'end_time' => $appointment->schedule->end_time,
                    ] : null,
                    'services' => $appointment->services->map(function ($service) {
                        return [
                            'id' => $service->id,
                            'title' => $service->title,
                            'min_price' => $service->min_price,
                        ];
                    }),
                    'created_at' => $appointment->created_at->toDateTimeString(),
                ];
            }),
            'stats' => $stats,
        ];

        return Inertia::render('Admin/Leads/LeadShow', [
            'lead' => $leadData
        ]);
    }

    /**
     * Calculate lead statistics
     */
    private function getLeadStats(Lead $lead)
    {
        $totalSpent = $lead->user ? $lead->user->orders->where('status', 'completed')->sum('total') : 0;
        $pendingOrders = $lead->user ? $lead->user->orders->where('status', 'pending')->count() : 0;
        $completedFormations = $lead->formations->where('pivot.status', 'completed')->count();
        $pendingFormations = $lead->formations->where('pivot.status', 'unpaid')->count();
        $upcomingAppointments = $lead->appointments->where('status', 'confirmed')->count();

        return [
            'total_spent' => $totalSpent,
            'orders_count' => $lead->user ? $lead->user->orders->count() : 0,
            'pending_orders' => $pendingOrders,
            'formations_count' => $lead->formations->count(),
            'completed_formations' => $completedFormations,
            'pending_formations' => $pendingFormations,
            'appointments_count' => $lead->appointments->count(),
            'upcoming_appointments' => $upcomingAppointments,
        ];
    }

    /**
     * Update lead formation status
     */
    public function updateFormationStatus(Request $request, Lead $lead, Formation $formation)
    {
        try {
            $request->validate([
                'status' => 'required|string|in:unpaid,paid,cancelled,completed',
                'paid_at' => 'nullable|date',
            ]);

            $lead->formations()->updateExistingPivot($formation->id, [
                'status' => $request->status,
                'paid_at' => $request->paid_at ?: null,
            ]);

            return back();

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour');
        }
    }

    /**
     * Update order status
     */
    public function updateOrderStatus(Request $request, Lead $lead, Order $order)
{
    try {
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,completed,cancelled',
        ]);

        if ($lead->user && $order->user_id === $lead->user->id) {
            $order->update(['status' => $request->status]);
            return back();
        }

        return back()->with('error', 'Commande non trouvée');

    } catch (\Exception $e) {
        return back()->with('error', 'Erreur lors de la mise à jour');
    }
}
    /**
     * Update appointment status
     */
    public function updateAppointmentStatus(Request $request, Lead $lead, Appointment $appointment)
    {
        try {
       
        $request->validate([
                'status' => 'required|string|in:pending,confirmed,cancelled,completed',
            ]);

            // Vérifier que le rendez-vous appartient bien au lead
            if ($appointment->lead_id === $lead->id) {
                $appointment->update(['status' => $request->status]);
                return back();
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour');
        }
    }

    /**
     * Remove the specified lead from storage.
     */
    public function destroy(Lead $lead)
    {
        // Détacher toutes les formations avant de supprimer le lead
        $lead->formations()->detach();
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('success', 'Lead supprimé avec succès.');
    }
}