<?php

namespace App\Http\Middleware\Web\V1;

use App\Models\Lang;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupère la locale de session, sinon la locale par défaut de l'application
        $locale = session('locale', config('app.locale'));

        // Vérifie que la langue est toujours active (sinon fallback sur la locale par défaut)
        $lang = Lang::where('code', $locale)->where('is_active', true)->first();

        // Applique la locale active, sinon la locale par défaut de l'application
        app()->setLocale($lang?->code ?? config('app.locale'));

        return $next($request);
    }
}
