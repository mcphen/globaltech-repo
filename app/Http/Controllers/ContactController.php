<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Setting;
use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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
            try {
                // Validation stricte
                $validated = $request->validate([
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'email' => 'required|email:rfc,dns|max:255',
                    'phone' => 'nullable|string|regex:/^[\+\d\s\-\(\)]{8,20}$/',
                    'subject' => 'required|string|max:255',
                    'description' => 'required|string|max:1000|min:10',
                ], [
                    'first_name.required' => 'Le prénom est obligatoire.',
                    'first_name.max' => 'Le prénom ne doit pas dépasser 255 caractères.',
                    'first_name.regex' => 'Le prénom ne doit contenir que des lettres, espaces et tirets.',
                    'last_name.required' => 'Le nom est obligatoire.',
                    'last_name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
                    'last_name.regex' => 'Le nom ne doit contenir que des lettres, espaces et tirets.',
                    'email.required' => 'L\'email est obligatoire.',
                    'email.email' => 'Veuillez saisir une adresse email valide.',
                    'email.max' => 'L\'email ne doit pas dépasser 255 caractères.',
                    'phone.regex' => 'Le format du téléphone est invalide. Exemple: +221 77 123 45 67',
                    'subject.required' => 'L\'objet est obligatoire.',
                    'subject.max' => 'L\'objet ne doit pas dépasser 255 caractères.',
                    'description.required' => 'Le message est obligatoire.',
                    'description.max' => 'Le message ne doit pas dépasser 1000 caractères.',
                    'description.min' => 'Le message doit contenir au moins 10 caractères.',
                ]);

                // Nettoyer les données
                $validated = array_map('trim', $validated);
                $validated['phone'] = $validated['phone'] ? preg_replace('/\s+/', ' ', $validated['phone']) : null;

                // Vérifier si l'email est valide (vérification supplémentaire)
                if (!filter_var($validated['email'], FILTER_VALIDATE_EMAIL)) {
                    throw ValidationException::withMessages([
                        'email' => 'L\'adresse email n\'est pas valide.'
                    ]);
                }

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
                        Mail::to($to)->send(new ContactMessage($validated));
                    } catch (\Throwable $e) {
                        Log::error('Erreur lors de l\'envoi du message de contact : ' . $e->getMessage());
                    }

                    return back()->with([
                        'success' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
                    ]);
                });

            } catch (ValidationException $e) {
                // Retourner les erreurs de validation
                return back()
                    ->withErrors($e->validator)
                    ->withInput();

            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi du message de contact : ' . $e->getMessage());

                return back()
                    ->withErrors([
                        'error' => 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.',
                    ])
                    ->withInput();
            }
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
