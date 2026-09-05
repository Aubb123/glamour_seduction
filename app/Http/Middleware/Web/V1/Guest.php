<?php

namespace App\Http\Middleware\Web\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Guest
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Middleware de non-authentification
        $user = $request->user();

        // Middleware de non-authentification
        if ($user && $user->id) {
            return redirect()->route('frontend.index')->with('error', 'Vous êtes déjà authentifié.');
        }

        return $next($request);
    }
}
