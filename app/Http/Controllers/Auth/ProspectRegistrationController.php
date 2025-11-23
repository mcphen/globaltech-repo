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
use Inertia\Inertia;
use Inertia\Response;

class ProspectRegistrationController extends Controller
{
    /**
     * Show the prospect registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/ProspectRegister',[
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    /**
     * Handle an incoming prospect registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required','string','max:255'],
            'last_name'  => ['required','string','max:255'],
            'email'      => ['required','string','lowercase','email','max:255', Rule::unique(User::class, 'email')],
            'phone'      => ['nullable','string','max:50'],
            'company'    => ['nullable','string','max:255'],
            'password'   => ['required','confirmed', Rules\Password::defaults()],
            'consent'    => ['accepted'],
        ]);

        // Create the user account
        $user = User::create([
            'name' => $validated['first_name'].' '.$validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_LEAD,
        ]);

        // Create the related lead entry
        Lead::create([
            'user_id'    => $user->id,
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            //'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'company'    => $validated['company'] ?? null,
            'consent_at' => now(),
        ]);

        return to_route('login')->with('status', 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
