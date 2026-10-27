<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects to the home page when a front page (contact, blog) is disabled
 * from the admin "Paramètres du site" screen.
 */
class EnsurePageEnabled
{
    public function handle(Request $request, Closure $next, string $page): Response
    {
        if (Setting::get("site_{$page}_enabled", '1') !== '1') {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
