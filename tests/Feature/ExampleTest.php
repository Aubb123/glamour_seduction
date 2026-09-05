<?php

/*
|--------------------------------------------------------------------------
| Smoke test du squelette
|--------------------------------------------------------------------------
|
| Le squelette Laravel teste la route `/`, mais le clone redirige `/` vers
| `frontend.index` (route V1, placeholder implémenté à l'Étape 16).
| On vérifie ici que l'application répond bien via son health-check `/up`,
| stable et indépendant de la base de données / du versioning.
|
*/

test('le health-check de l’application répond HTTP 200', function () {
    $this->get('/up')->assertOk();
});
