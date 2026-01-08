<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Affiche la page de demande de réinitialisation de mot de passe.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Traite la demande de réinitialisation de mot de passe.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            // Message personnalisé pour le succès
            return back()->with([
                'status' => 'Un lien de réinitialisation a été envoyé à votre adresse email.',
                'success' => 'Un lien de réinitialisation a été envoyé à votre adresse email.'
            ]);
        }

        // Messages d'erreur personnalisés en français
        $messages = [
            Password::INVALID_USER => 'Aucun compte ne correspond à cette adresse email.',
            Password::INVALID_TOKEN => 'Le lien de réinitialisation est invalide ou a expiré.',
            Password::RESET_THROTTLED => 'Trop de tentatives. Veuillez réessayer plus tard.',
        ];

        return back()->withErrors([
            'email' => $messages[$status] ?? 'Une erreur est survenue. Veuillez réessayer.',
            'error' => $messages[$status] ?? 'Une erreur est survenue. Veuillez réessayer.'
        ]);
    }
}