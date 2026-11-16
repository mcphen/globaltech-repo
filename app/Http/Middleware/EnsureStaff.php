<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Réserve l'administration au personnel (rôles admin et user).
 * Les prospects (rôle lead) sont renvoyés vers leur espace.
 */
class EnsureStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role === User::ROLE_LEAD) {
            if ($request->expectsJson()) {
                abort(403, 'Accès réservé à l\'administration.');
            }

            return $user
                ? redirect()->route('prospect.dashboard')
                : redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
