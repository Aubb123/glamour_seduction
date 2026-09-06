<?php

declare(strict_types=1);

use App\Models\User;

/**
 * Étape 15 — Analytics (`backend.analytics`).
 *
 * Ce contrôleur utilise DATE_FORMAT() (SQL MySQL pur) : le test s'exécute
 * sur la connexion MySQL de recette (`base_clone`, seedée par la recette
 * finale) en LECTURE SEULE — aucune mutation de données.
 */
beforeEach(function () {
    // Connexion MySQL de recette (base_clone est seedée par migrate:fresh --seed)
    config(['database.default' => 'mysql']);
    config(['database.connections.mysql.database' => 'base_clone']);
    DB::purge('mysql');

    // Le boot initial s'est fait sur la connexion par défaut (SQLite vide) :
    // les routes v1 n'ont pas été chargées. On les recharge maintenant que
    // MySQL (base_clone, version v1 active) est la connexion par défaut.
    bootV1WebRoutes();
});

it('affiche la page analytics (période par défaut)', function () {
    $admin = User::where('email', 'admin@site.com')->first();

    $this->actingAs($admin)
        ->get(route('backend.analytics'))
        ->assertOk()
        ->assertSee('labels');
});

it('accepte les paramètres de période (week, month, year)', function () {
    $admin = User::where('email', 'admin@site.com')->first();

    foreach (['week', 'month', 'year'] as $period) {
        $this->actingAs($admin)
            ->get(route('backend.analytics', ['period' => $period]))
            ->assertOk();
    }
});

it('protege analytics (non connecté → login)', function () {
    $this->get('/backend/dashboard-analytics')->assertRedirect(route('login'));
});