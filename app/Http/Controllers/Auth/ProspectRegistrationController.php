<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProspectRegistrationController extends Controller
{
    /**
     * Affiche la page d'inscription prospect.
     */
    public function create(): Response
    {
        return Inertia::render('auth/ProspectRegister', [
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    /**
     * Traite une demande d'inscription prospect.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255', 'min:2'],
                'last_name'  => ['required', 'string', 'max:255', 'min:2'],
                'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
                'phone'      => ['nullable', 'string', 'max:20', 'regex:/^[0-9\s\-\+\(\)]{10,20}$/'],
                'company'    => ['nullable', 'string', 'max:255'],
                'password'   => ['required', 'confirmed', Rules\Password::defaults()->min(8)->mixedCase()->numbers()],
                'consent'    => ['required', 'accepted'],
            ], [
                // Messages personnalisés 
                'first_name.required' => 'Le prénom est obligatoire.',
                'first_name.min' => 'Le prénom doit contenir au moins 2 caractères.',
                'last_name.required' => 'Le nom est obligatoire.',
                'last_name.min' => 'Le nom doit contenir au moins 2 caractères.',
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.email' => 'Veuillez saisir une adresse email valide.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'phone.regex' => 'Veuillez saisir un numéro de téléphone valide.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.confirmed' => 'Les mots de passe ne correspondent pas.',
                'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
                'consent.required' => 'Vous devez accepter les conditions d\'utilisation.',
                'consent.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
            ]);

            // Création du compte utilisateur
            $user = User::create([
                'name' => trim($validated['first_name'] . ' ' . $validated['last_name']),
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_LEAD,
            ]);

            // Création de l'entrée lead associée
            Lead::create([
                'user_id'    => $user->id,
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'phone'      => $validated['phone'] ?? null,
                'company'    => $validated['company'] ?? null,
                'consent_at' => now(),
                'status' => 'new', // Statut par défaut
            ]);

            return to_route('login')->with([
                'status' => 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.',
                'success' => 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.'
            ]);

        } catch (ValidationException $e) {
            // Rethrow pour qu'Inertia gère les erreurs
            throw $e;
            
        } catch (\Exception $e) {
            // Erreur générale
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la création de votre compte. Veuillez réessayer.',
            ]);
        }
    }
}