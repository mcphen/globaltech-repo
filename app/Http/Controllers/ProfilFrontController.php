<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfilFrontController extends Controller
{

    public function index()
    {
        return Inertia::render('Front/Profile', [
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    public function edit()
    {
        return Inertia::render('Front/ProfileEdit', [
            'contactSettings' => $this->getContactSettings()
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        try {
            $user->update($validated);

            return redirect()->route('front.profile.edit')->with([
                'success' => 'Vos informations ont été mises à jour avec succès.'
            ]);

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la mise à jour.'
            ]);
        }
    }

   
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return redirect()->route('front.profile.edit')->with([
                'success' => 'Votre mot de passe a été mis à jour avec succès.'
            ]);

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la mise à jour du mot de passe.'
            ]);
        }
    }
}
