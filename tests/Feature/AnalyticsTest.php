<?php

declare(strict_types=1);

use App\Models\User;

/**
 * Étape 15 — Analytics (`backend.analytics`).
 *
 * Ce contrôleur utilise DATE_FORMAT() (SQL MySQL pur) : le test s'exécute
 * sur la connexion MySQL de recette du projet (celle du `.env`) en LECTURE
 * SEULE — aucune mutation de données.
 *
 * NB : on lit le nom de la base directement dans le FICHIER `.env` car
 * phpunit.xml définit DB_DATABASE=:memory: (SQLite) pour la suite.
 */
function analyticsRecetteDatabase(): string
{
    foreach (file(base_path('.env'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), 'DB_DATABASE=')) {
            $value = trim(substr(trim($line), strlen('DB_DATABASE=')), " \"'");
            if ($value !== '') {
                return $value;
            }
        }
    }

    return 'base_clone';
}

beforeEach(function () {
    // Connexion MySQL de recette du projet (DB_DATABASE du .env : `base_clone`
    // ici, `base` sur le projet d'origine) — utilisée en LECTURE SEULE.
    config(['database.default' => 'mysql']);
    config(['database.connections.mysql.database' => analyticsRecetteDatabase()]);
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
        ->assertSee('labels')
        ->assertSee('chart.min.js'); // Chart.js doit être chargé pour les graphiques
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