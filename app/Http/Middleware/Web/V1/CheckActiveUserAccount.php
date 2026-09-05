<?php

namespace App\Http\Middleware\Web\V1;

use App\Enums\Users\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveUserAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Vérifie si le compte de l'utilisateur est actif (ni suspendu, ni banni)
        if ($user && $user->status !== UserStatus::Active) {
            return redirect()->route('account.disabled')->with('error', 'Votre compte a été désactivé. Veuillez contacter le support.');
        }

        return $next($request);
    }
}
