<?php

namespace App\Http\Middleware\Web\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPhone
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Vérifie si le numéro de téléphone de l'utilisateur est vérifié
        // (le téléphone étant optionnel, on ne contrôle que s'il est renseigné)
        if ($user && ! is_null($user->phone) && is_null($user->phone_verified_at)) {
            return redirect()->route('phone.verifie.use-otp');
        }

        return $next($request);
    }
}
