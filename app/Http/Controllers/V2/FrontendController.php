<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\V1\FrontendController as V1FrontendController;
use Illuminate\View\View;

class FrontendController extends V1FrontendController
{
    // Tout est hérité de V1 pour l'instant
    // Tu redéfinis ici uniquement ce qui change en V2

    public function frontend_index(): View
    {
        dd('Bienvenue sur la page d’accueil de la version 2 !');

        return view('v2/frontend/pages/welcome');
    }
}
