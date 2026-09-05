<?php

namespace App\Http\Middleware\Web\V1;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Vérifie si l'email de l'utilisateur est vérifié
        if ($user && is_null($user->email_verified_at)) {
            return redirect()->route('email.verifie.use-otp');
        }

        return $next($request);
    }
}
