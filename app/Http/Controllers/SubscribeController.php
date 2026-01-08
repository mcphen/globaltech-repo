<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SubscribeController extends Controller
{
    public function index()
    {
        $subscribes = Subscribe::orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Admin/Subscribes/SubscribeIndex', [
            'subscribes' => $subscribes,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => [
                    'required', 
                    'email:rfc,dns', 
                    'max:255',
                    'unique:subscribes,email'
                ],
            ], [
                'email.required' => 'L\'adresse email est obligatoire.',
                'email.email' => 'Veuillez saisir une adresse email valide.',
                'email.unique' => 'Cette adresse email est déjà abonnée.',
                'email.max' => 'L\'adresse email ne doit pas dépasser 255 caractères.',
            ]);

            Subscribe::create([
                'email' => $validated['email'],
                'subscribed_at' => now(),
            ]);

            return back()->with([
                'success' => 'Abonnement réussi ! Merci de vous être abonné à notre newsletter.',
                'status' => 'Abonnement réussi ! Merci de vous être abonné à notre newsletter.'
            ]);

        } catch (ValidationException $e) {
            // Pour qu'Inertia puisse gérer les erreurs de validation
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'abonnement : ' . $e->getMessage());
            return back()->withErrors([
                'email' => 'Une erreur est survenue. Veuillez réessayer.',
                'error' => 'Une erreur est survenue. Veuillez réessayer.'
            ]);
        }
    }

    public function destroy(Subscribe $subscribe)
    {
        $subscribe->delete();
        return back()->with('success', 'Abonnement supprimé avec succès.');
    }
}