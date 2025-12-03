<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Appel;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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
        $order->load(['items', 'user', 'appels.user']);
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



    /**
     * Générer une facture PDF
     */
public function generateInvoice(Order $order)
{
    // Générer le numéro de facture si pas déjà fait
    if (!$order->invoice_number) {
        $order->update([
            'invoice_number' => $order->generateInvoiceNumber(),
            'invoice_date' => now(),
        ]);
    }

    // Charger les données nécessaires
    $order->load(['items', 'user']);
    
    // Récupérer les informations de l'entreprise depuis les settings
    $companySettings = [
        'name' => Setting::get('company_name', config('app.name')),
        'address' => Setting::get('company_address'),
        'phone' => Setting::get('contact_phone'),
        'email' => Setting::get('contact_email'),
    ];

    // Générer le PDF
    $pdf = Pdf::loadView('pdf.invoice', [
        'order' => $order,
        'company' => $companySettings
    ]);

    // Nom du fichier
    $filename = "invoice-{$order->invoice_number}.pdf";
    $path = "invoices/{$filename}";

    // Sauvegarder le PDF
    Storage::disk('public')->put($path, $pdf->output());

    // Mettre à jour l'ordre
    $order->update(['invoice_path' => $path]);

    return response()->json([
        'success' => true,
        'message' => 'Facture générée avec succès',
        'invoice_path' => Storage::url($path),
        'order' => $order->fresh(),
    ]);
}

    /**
     * Télécharger la facture
     */
    public function downloadInvoice(Order $order)
    {
        if (!$order->invoice_path || !Storage::disk('public')->exists($order->invoice_path)) {
            abort(404, 'Facture non trouvée');
        }

        return Storage::disk('public')->download($order->invoice_path, "facture-{$order->invoice_number}.pdf");
    }

    /**
     * Ajouter un nouvel appel
     */
    public function storeAppel(Request $request, Order $order)
    {
        $data = $request->validate([
            'type' => 'required|in:entrant,sortant',
            'status' => 'required|in:répondu,non-répondu,rappel-prévu',
            'duration' => 'nullable|integer|min:0',
            'notes' => 'required|string|max:2000',
            'next_call_at' => 'nullable|date',
        ]);

        // Créer l'appel
        $appel = $order->appels()->create([
            'user_id' => auth()->id(),
            'called_at' => now(),
            'type' => $data['type'],
            'status' => $data['status'],
            'duration' => $data['duration'],
            'notes' => $data['notes'],
            'next_call_at' => $data['next_call_at'] ?? null,
        ]);

        $appel->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Appel enregistré avec succès',
            'appel' => $appel,
        ]);
    }

    /**
     * Lister les appels d'une commande
     */
    public function listAppels(Order $order)
    {
        $appels = $order->appels()->with('user')->latest()->paginate(10);
        
        return response()->json($appels);
    }

    /**
     * Supprimer un appel
     */
    public function deleteAppel(Order $order, Appel $appel)
    {
        // Vérifier que l'appel appartient bien à cette commande
        if ($appel->callable_type !== Order::class || $appel->callable_id !== $order->id) {
            abort(403, 'Cet appel ne correspond pas à cette commande');
        }

        $appel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appel supprimé avec succès',
        ]);
    }
}
