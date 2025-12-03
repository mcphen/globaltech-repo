<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class ContactController extends Controller
{
    /**
     * Display a listing of the contacts in admin panel.
     */
    public function adminIndex(Request $request)
{
    // Récupérer le filtre de statut depuis la requête
    $status = $request->input('status');
    
    // Construire la requête de base
    $query = Contact::with(['client']);
    
    // Appliquer le filtre de statut si spécifié et valide
    if ($status && in_array($status, ['pending', 'processed', 'archived'])) {
        $query->where('status', $status);
    }
    
    // Trier par date de création décroissante
    $contacts = $query->orderBy('created_at', 'desc')
                      ->paginate(10);
    
    // Ajouter le paramètre de statut à la pagination pour préserver le filtre
    if ($status) {
        $contacts->appends(['status' => $status]);
    }

    return Inertia::render('Admin/Contacts/ContactIndex', [
        'contacts' => $contacts,
        'filters' => [
            'status' => $status
        ]
    ]);
}

    /**
     * Display the specified contact in admin panel.
     */
    public function show(Contact $contact)
    {
        // Load the contact with its relationships
        $contact->load(['client']);

        return Inertia::render('Admin/Contacts/ContactShow', [
            'contact' => $contact,
        ]);
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(Contact $contact)
    {
        // Delete the contact
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Contact supprimé avec succès.');
    }


    /**
     * Display the contact page.
     */
    public function index()
    {
        return Inertia::render('Front/Contact', [
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    /**
     * Store a new contact message.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        // Start a database transaction
        return DB::transaction(function () use ($validated) {
            // Create or find the client
            $client = Client::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'phone' => $validated['phone'] ?? null,
                    'is_lead' => true,
                ]
            );

            // Create the contact message
            Contact::create([
                'client_id' => $client->id,
                'subject' => $validated['subject'],
                'description' => $validated['description'],
            ]);

            // Send notification email to the recipient
            try {
                $to = ['glainnguema@gmail.com'];
                Mail::to($to)->bcc('glainnguema@gmail.com')->send(new ContactMessage($validated));
            } catch (\Throwable $e) {
                Log::error('Erreur lors de l\'envoi du message de contact : ' . $e->getMessage());
            }

            return back()->with('success', 'Votre message a été envoyé avec succès.');
        });

        
    }

    /**
 * Update the status of the specified contact.
 */

public function updateStatus(Request $request, Contact $contact)
{
    $validated = $request->validate([
        'status' => 'required|in:pending,processed,archived',
        'notes' => 'nullable|string|max:1000',
    ]);

    try {
        // Update the contact
        $contact->update([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? $contact->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'contact' => $contact->fresh()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
        ], 500);
    }
}
}
