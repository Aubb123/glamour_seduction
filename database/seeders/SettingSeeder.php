<?php

namespace Database\Seeders;

use App\Models\Lang;
use App\Models\Setting;
use App\Models\SettingTranslation;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // Liste des réglages de base à insérer ou mettre à jour.
        // Chaque élément contient un groupe, une clé unique, un type, et éventuellement des traductions.
        //
        // ════════════════════════════════════════════════════════════════════
        // TROIS CAS D'USAGE :
        // ════════════════════════════════════════════════════════════════════
        //
        // RÈGLE `is_translatable` (uniquement la VALEUR, pas le libellé) :
        //   → true  : la valeur diffère selon la langue (chaînes traduisibles, ex. site_name).
        //   → false : la valeur est identique pour toutes les langues (integer, boolean,
        //             float/décimal, seuils, limites techniques…).
        //   Les traductions `name`/`description` peuvent exister quel que soit le flag :
        //   elles servent uniquement à l'affichage dans le back-office.
        //
        // CAS 1 — Valeur partagée (même valeur pour toutes les langues) :
        //   → Renseigner `value` sur le setting principal.
        //   → `is_translatable = false`.
        //   → Mettre `value => null` dans les traductions (seuls `name` et
        //     `description` sont traduits).
        //   → Exemple : limite technique, seuil, paramètre système.
        //
        // CAS 2 — Valeur différente par langue (chaînes uniquement) :
        //   → `is_translatable = true` + chaque traduction possède sa propre `value`.
        //   → La `value` du setting principal sert uniquement de fallback
        //     si une traduction est absente (ex: nouvelle langue ajoutée).
        //   → Exemple : nom du site, slogan, meta descriptions.
        //
        // CAS 3 - Valeur partagée mais non traduisible (is_translatable = false) :
        //  → La valeur est la même pour toutes les langues et n'a pas de traduction de valeur.
        //  → Exemple : mode maintenance, paramètres techniques, clés API.
        //  → Le tableau `translations` peut rester vide ou ne contenir que `name`/`description`.
        //
        // `is_editable` : indique si le paramètre est modifiable dans le backend.
        //  → true : l'utilisateur peut modifier la valeur.
        //  → false : le champ est désactivé dans le formulaire (lecture seule).
        //

        $settings = [
            // ──────────────────────────────────────────────────────────────
            // CAS 1 — Exemple : limite technique (valeur partagée)
            // ──────────────────────────────────────────────────────────────
            // Nombre maximum de fichiers cloudfiles non synchronisés avant redirection
            [
                'group' => 'cloudfiles',
                'key' => 'max_unsynced_files_before_redirect',
                'value' => 10, // Valeur partagée, identique pour toutes les langues
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => false,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nombre maximum de fichiers non synchronisés avant redirection', 'value' => null, 'description' => 'Le nombre maximum de fichiers cloudfiles non synchronisés avant que l\'utilisateur soit redirigé vers la page de synchronisation.'],
                    ['lang_code' => 'en', 'name' => 'Maximum unsynced files before redirect', 'value' => null, 'description' => 'The maximum number of unsynced cloudfiles before the user is redirected to the sync page.'],
                ],
            ],
            // Nombres maximum de fichiers cloudfiles à synchroniser en une seule fois
            [
                'group' => 'cloudfiles',
                'key' => 'max_files_sync_batch',
                'value' => 10, // Valeur partagée, identique pour toutes les langues
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => false,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nombre maximum de fichiers à synchroniser en une seule fois', 'value' => null, 'description' => 'Le nombre maximum de fichiers cloudfiles à synchroniser en une seule opération.'],
                    ['lang_code' => 'en', 'name' => 'Maximum files to sync in one batch', 'value' => null, 'description' => 'The maximum number of cloudfiles to sync in a single operation.'],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // Délai de réessai après un échec de synchronisation (minutes)
            // ──────────────────────────────────────────────────────────────
            // Utilisé par CloudFile::scopeRetryable() — temps d'attente avant de
            // retenter la synchronisation d'un fichier cloud en pending/error.
            [
                'group' => 'cloudfiles',
                'key' => 'retry_sync_after_minutes',
                'value' => 10, // Valeur partagée, identique pour toutes les langues
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => false,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Délai de réessai de synchronisation (minutes)', 'value' => null, 'description' => 'Le nombre de minutes à attendre après une tentative avant de retenter la synchronisation d\'un fichier cloud en échec (pending/error).'],
                    ['lang_code' => 'en', 'name' => 'Sync retry delay (minutes)', 'value' => null, 'description' => 'The number of minutes to wait after an attempt before retrying the sync of a failed cloudfile (pending/error).'],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // Délai avant remise en file d'un fichier bloqué en "processing"
            // ──────────────────────────────────────────────────────────────
            // Utilisé par CloudFile::resetStuckProcessing() — nombre de minutes
            // après lequel un fichier resté bloqué en "processing" est repassé
            // en "pending" par la commande cloud:sync.
            [
                'group' => 'cloudfiles',
                'key' => 'reset_stuck_processing_after_minutes',
                'value' => 30, // Valeur partagée, identique pour toutes les langues
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => false,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Délai avant remise en file des fichiers bloqués (minutes)', 'value' => null, 'description' => 'Le nombre de minutes après lequel un fichier resté bloqué en "processing" est automatiquement repassé en "pending" par la commande cloud:sync.'],
                    ['lang_code' => 'en', 'name' => 'Delay before requeueing stuck files (minutes)', 'value' => null, 'description' => 'The number of minutes after which a file stuck in "processing" is automatically reset to "pending" by the cloud:sync command.'],
                ],
            ],
            // ──────────────────────────────────────────────────────────────
            // Durée de validité des URL temporaires
            // ──────────────────────────────────────────────────────────────
            // Utilisé par CloudStorageService::temporaryUrl() — durée de vie
            // des URL temporaires signées avant leur expiration.
            [
                'group' => 'cloudfiles',
                'key' => 'temporary_url_expires_in_minutes',
                'value' => 25, // Valeur partagée, identique pour toutes les langues
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => false,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Durée de validité des URL temporaires (minutes)', 'value' => null, 'description' => 'Le nombre de minutes pendant lesquelles une URL temporaire signée reste valide avant expiration.'],
                    ['lang_code' => 'en', 'name' => 'Temporary URL validity (minutes)', 'value' => null, 'description' => 'The number of minutes a signed temporary URL remains valid before expiring.'],
                ],
            ],

            // ──────────────────────────────────────────────────────────────
            // Nombre maximum de pays insérés par exécution du GeoCountrySeeder
            // ──────────────────────────────────────────────────────────────
            // Utilisé par GeoCountrySeeder — limite le nombre de pays insérés
            // en une seule exécution en environnement local pour accélérer le seeding.
            [
                'group' => 'geo',
                'key' => 'max_countries_per_seed',
                'value' => 25, // Valeur partagée, identique pour toutes les langues
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => false,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nombre maximum de pays par seed', 'value' => null, 'description' => 'Le nombre maximum de pays insérés en une seule exécution du GeoCountrySeeder en environnement local.'],
                    ['lang_code' => 'en', 'name' => 'Maximum number of countries per seed', 'value' => null, 'description' => 'The maximum number of countries inserted in a single GeoCountrySeeder run in local environment.'],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // GROUPE retention — purge des historiques
            // ══════════════════════════════════════════════════════════════
            // Durée de conservation des logs d'activité (jours)
            [
                'group' => 'retention',
                'key' => 'activity_logs_retention_days',
                'value' => 3,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => "Conservation des logs d'activité (jours)", 'value' => null, 'description' => 'Nombre de jours de conservation des logs d\'activité avant purge.'],
                    ['lang_code' => 'en', 'name' => 'Activity logs retention (days)', 'value' => null, 'description' => 'Number of days activity logs are kept before being purged.'],
                ],
            ],
            // Durée de conservation des audits (jours)
            [
                'group' => 'retention',
                'key' => 'audits_retention_days',
                'value' => 3,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Conservation des audits (jours)', 'value' => null, 'description' => 'Nombre de jours de conservation des audits avant purge.'],
                    ['lang_code' => 'en', 'name' => 'Audits retention (days)', 'value' => null, 'description' => 'Number of days audits are kept before being purged.'],
                ],
            ],
            // ══════════════════════════════════════════════════════════════
            // GROUPE security — authentification
            // ══════════════════════════════════════════════════════════════
            // Longueur minimale du mot de passe
            [
                'group' => 'security',
                'key' => 'min_password_length',
                'value' => 8,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Longueur minimale du mot de passe', 'value' => null, 'description' => 'Nombre minimum de caractères exigé pour un mot de passe.'],
                    ['lang_code' => 'en', 'name' => 'Minimum password length', 'value' => null, 'description' => 'Minimum number of characters required for a password.'],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // GROUPE blogs — limites du module Blog
            // ══════════════════════════════════════════════════════════════
            // Nombre maximum de tags par article
            [
                'group' => 'blogs',
                'key' => 'max_blog_post_tags',
                'value' => 5,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => "Nombre maximum de tags par article", 'value' => null, 'description' => "Nombre maximum de tags pouvant être associés à un article de blog."],
                    ['lang_code' => 'en', 'name' => 'Maximum tags per blog post', 'value' => null, 'description' => 'Maximum number of tags that can be attached to a blog post.'],
                ],
            ],
            // Vitesse de lecture utilisée pour calculer le temps de lecture
            [
                'group' => 'blogs',
                'key' => 'blog_reading_words_per_minute',
                'value' => 200,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Vitesse de lecture (mots par minute)', 'value' => null, 'description' => 'Nombre de mots lus par minute utilisé pour estimer le temps de lecture d\'un article.'],
                    ['lang_code' => 'en', 'name' => 'Reading speed (words per minute)', 'value' => null, 'description' => 'Number of words read per minute used to estimate an article reading time.'],
                ],
            ],
            // ══════════════════════════════════════════════════════════════
            // GROUPE dashboard — affichage du tableau de bord
            // ══════════════════════════════════════════════════════════════
            // Nombre d'éléments affichés dans les listes « derniers … »
            [
                'group' => 'dashboard',
                'key' => 'dashboard_latest_items_limit',
                'value' => 5,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nombre d\'éléments récents du dashboard', 'value' => null, 'description' => 'Nombre d\'éléments affichés dans les listes « derniers utilisateurs, contacts, articles, abonnés ».'],
                    ['lang_code' => 'en', 'name' => 'Dashboard latest items count', 'value' => null, 'description' => 'Number of items displayed in the “latest users, contacts, posts, subscribers” lists.'],
                ],
            ],
            // Nombre de lignes par défaut des tableaux DataTables
            [
                'group' => 'dashboard',
                'key' => 'datatable_default_page_length',
                'value' => 25,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Lignes par page des tableaux', 'value' => null, 'description' => 'Nombre de lignes affichées par défaut dans les tableaux DataTables du back-office.'],
                    ['lang_code' => 'en', 'name' => 'Table rows per page', 'value' => null, 'description' => 'Default number of rows displayed in backend DataTables.'],
                ],
            ],

            // Longueur maximale des notes internes d'un entrepôt
            [
                'group' => 'products',
                'key' => 'max_product_warehouse_notes_length',
                'value' => 2000,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => "Longueur maximale des notes d'entrepôt (caractères)", 'value' => null, 'description' => "Nombre maximum de caractères autorisé pour les notes internes d'un entrepôt."],
                    ['lang_code' => 'en', 'name' => 'Maximum warehouse notes length (characters)', 'value' => null, 'description' => 'Maximum number of characters allowed for the internal notes of a warehouse.'],
                ],
            ],
            // Nombre maximum de tags par produit
            [
                'group' => 'products',
                'key' => 'max_product_tags',
                'value' => 5,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nombre maximum de tags par produit', 'value' => null, 'description' => 'Nombre maximum de tags pouvant être associés à un produit.'],
                    ['lang_code' => 'en', 'name' => 'Maximum tags per product', 'value' => null, 'description' => 'Maximum number of tags that can be attached to a product.'],
                ],
            ],
            // Prix maximum autorisé pour un produit ou une variante
            [
                'group' => 'products',
                'key' => 'max_product_price',
                'value' => '99999999.99', // Type float : valeur décimale
                'type' => 'float',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Prix maximum autorisé', 'value' => null, 'description' => 'Montant maximum autorisé pour le prix de base, le prix barré et le prix des variantes.'],
                    ['lang_code' => 'en', 'name' => 'Maximum allowed price', 'value' => null, 'description' => 'Maximum amount allowed for the base price, compare-at price and variant price.'],
                ],
            ],
            // Poids maximum autorisé pour un produit
            [
                'group' => 'products',
                'key' => 'max_product_weight',
                'value' => '99999.99', // Type float : valeur décimale
                'type' => 'float',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Poids maximum autorisé (kg)', 'value' => null, 'description' => 'Poids maximum autorisé pour un produit, exprimé en kilogrammes.'],
                    ['lang_code' => 'en', 'name' => 'Maximum allowed weight (kg)', 'value' => null, 'description' => 'Maximum weight allowed for a product, expressed in kilograms.'],
                ],
            ],

            // ══════════════════════════════════════════════════════════════
            // GROUPE uploads — limites des fichiers téléversés
            // ══════════════════════════════════════════════════════════════
            // Taille maximale des images téléversées (en kilooctets ; 2048 Ko = 2 Mo)
            [
                'group' => 'uploads',
                'key' => 'max_image_size_kb',
                'value' => 2048,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Taille maximale des images (Ko)', 'value' => null, 'description' => 'Taille maximale autorisée pour chaque image téléversée, en kilooctets (2048 Ko = 2 Mo).'],
                    ['lang_code' => 'en', 'name' => 'Maximum image size (KB)', 'value' => null, 'description' => 'Maximum size allowed for each uploaded image, in kilobytes (2048 KB = 2 MB).'],
                ],
            ],
            // ══════════════════════════════════════════════════════════════
            // GROUPE products — limites du module Produits
            // ══════════════════════════════════════════════════════════════
            // Nombre maximum d'images principales par produit
            [
                'group' => 'products',
                'key' => 'max_product_images',
                'value' => 4,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => "Nombre maximum d'images par produit", 'value' => null, 'description' => "Nombre maximum d'images principales qu'un produit peut contenir."],
                    ['lang_code' => 'en', 'name' => 'Maximum images per product', 'value' => null, 'description' => 'Maximum number of main images a product can contain.'],
                ],
            ],
            // Nombre minimum d'images principales par produit
            [
                'group' => 'products',
                'key' => 'min_product_images',
                'value' => 1,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => "Nombre minimum d'images par produit", 'value' => null, 'description' => "Nombre minimum d'images qu'un produit doit conserver (suppression de la dernière image refusée)."],
                    ['lang_code' => 'en', 'name' => 'Minimum images per product', 'value' => null, 'description' => 'Minimum number of images a product must keep (deleting the last image is refused).'],
                ],
            ],
            // Nombre maximum d'images par variante de produit
            [
                'group' => 'products',
                'key' => 'max_product_variant_images',
                'value' => 4,
                'type' => 'integer',
                'is_public' => false,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => "Nombre maximum d'images par variante", 'value' => null, 'description' => "Nombre maximum d'images qu'une variante de produit peut contenir."],
                    ['lang_code' => 'en', 'name' => 'Maximum images per variant', 'value' => null, 'description' => 'Maximum number of images a product variant can contain.'],
                ],
            ],

            // ──────────────────────────────────────────────────────────────
            // CAS 2 — Exemple : nom du site (traduit par langue)
            // ──────────────────────────────────────────────────────────────
            [
                'group' => 'general',
                'key' => 'site_name',
                'value' => null, // Fallback si une traduction est absente
                'type' => 'string',
                'is_public' => true,
                'is_translatable' => true,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Nom du site', 'value' => 'Base Laravel fr', 'description' => 'Le nom principal du site affiché dans l\'en-tête et les onglets du navigateur.'],
                    ['lang_code' => 'en', 'name' => 'Site name', 'value' => 'Base Laravel en', 'description' => 'The main name of the site displayed in the header and browser tabs.'],
                ],
            ],

            // ──────────────────────────────────────────────────────────────
            // CAS 3 — Exemple : mode maintenance (valeur partagée, non traduisible)
            // ──────────────────────────────────────────────────────────────
            [
                // Activation du mode maintenance
                'group' => 'general',
                'key' => 'maintenance_mode',
                'value' => false,
                'type' => 'boolean',
                'is_public' => true,
                'is_translatable' => false,
                'is_editable' => true,
                'translations' => [
                    ['lang_code' => 'fr', 'name' => 'Mode maintenance', 'value' => null, 'description' => 'Indique si le site est en mode maintenance.'],
                    ['lang_code' => 'en', 'name' => 'Maintenance mode', 'value' => null, 'description' => 'Indicates whether the site is in maintenance mode.'],
                ],
            ],

        ];

        // Boucle principale d'insertion / mise à jour des réglages.
        foreach ($settings as $data) {
            // Les traductions sont traitées séparément après la création ou mise à jour du setting principal.
            $translations = $data['translations'] ?? [];
            unset($data['translations']);

            // Crée le setting s'il n'existe pas, sinon le met à jour.
            $setting = Setting::updateOrCreate(
                ['group' => $data['group'], 'key' => $data['key']],
                [
                    'value' => $data['value'],
                    'type' => $data['type'],
                    'is_public' => $data['is_public'],
                    'is_translatable' => $data['is_translatable'],
                    'is_editable' => $data['is_editable'] ?? true,
                ]
            );

            // Ajoute ou met à jour les traductions associées à chaque setting.
            foreach ($translations as $translation) {
                $lang = Lang::where('code', $translation['lang_code'])->first();

                if (! $lang) {
                    $this->command->warn("Langue '{$translation['lang_code']}' non trouvée pour le setting '{$setting->key}'. Traduction ignorée.");

                    continue;
                }

                SettingTranslation::updateOrCreate(
                    ['setting_id' => $setting->id, 'lang_id' => $lang->id],
                    [
                        'name' => $translation['name'],
                        'value' => $translation['value'],
                        'description' => $translation['description'] ?? null,
                    ]
                );
            }
        }
    }

    // /**
    //  * Exécute le seeder.
    //  */
    // public function run(): void
    // {
    //     // Liste des réglages de base à insérer ou mettre à jour.
    //     // Chaque élément contient un groupe, une clé unique, un type, et éventuellement des traductions.
    //     $settings = [
    //         [
    //             // Paramètres généraux du site
    //             'group' => 'general',
    //             'key' => 'site_name',
    //             'value' => null,
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => true,
    //             'translations' => [
    //                 ['lang_code' => 'fr', 'name' => 'Nom du site', 'value' => 'Base Laravel', 'description' => 'Le nom principal du site affiché dans l\'en-tête et les onglets du navigateur.'],
    //                 ['lang_code' => 'en', 'name' => 'Site name', 'value' => 'Base Laravel', 'description' => 'The main name of the site displayed in the header and browser tabs.'],
    //             ],
    //         ],
    //         [
    //             // Slogan affiché sur le site
    //             'group' => 'general',
    //             'key' => 'site_slogan',
    //             'value' => null,
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => true,
    //             'translations' => [
    //                 ['lang_code' => 'fr', 'name' => 'Slogan du site', 'value' => 'Bienvenue sur Base Laravel', 'description' => 'Le slogan ou tagline affiché sous le nom du site.'],
    //                 ['lang_code' => 'en', 'name' => 'Site slogan', 'value' => 'Welcome to Base Laravel', 'description' => 'The slogan or tagline displayed below the site name.'],
    //             ],
    //         ],
    //         [
    //             'group' => 'general',
    //             'key' => 'site_url',
    //             'value' => 'https://www.base-laravel.com',
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'general',
    //             'key' => 'site_logo',
    //             'value' => null,
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'general',
    //             'key' => 'site_favicon',
    //             'value' => null,
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'general',
    //             'key' => 'default_lang',
    //             'value' => 'fr',
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             // Activation du mode maintenance
    //             'group' => 'general',
    //             'key' => 'maintenance_mode',
    //             'value' => false,
    //             'type' => 'boolean',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'mail',
    //             'key' => 'mail_from_address',
    //             'value' => 'no-reply@monsite.com',
    //             'type' => 'string',
    //             'is_public' => false,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'mail',
    //             'key' => 'mail_from_name',
    //             'value' => 'Base Laravel',
    //             'type' => 'string',
    //             'is_public' => false,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'mail',
    //             'key' => 'mail_contact',
    //             'value' => 'contact@base-laravel.com',
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'security',
    //             'key' => 'max_login_attempts',
    //             'value' => 5,
    //             'type' => 'integer',
    //             'is_public' => false,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'security',
    //             'key' => 'session_lifetime',
    //             'value' => 120,
    //             'type' => 'integer',
    //             'is_public' => false,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'security',
    //             'key' => 'registration_open',
    //             'value' => true,
    //             'type' => 'boolean',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'upload',
    //             'key' => 'max_upload_size',
    //             'value' => 5120,
    //             'type' => 'integer',
    //             'is_public' => false,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'upload',
    //             'key' => 'allowed_extensions',
    //             'value' => 'jpg,png,mp4',
    //             'type' => 'string',
    //             'is_public' => false,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'social',
    //             'key' => 'social_facebook',
    //             'value' => 'https://facebook.com/base-laravel',
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'social',
    //             'key' => 'social_instagram',
    //             'value' => 'https://instagram.com/base-laravel',
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             'group' => 'social',
    //             'key' => 'social_twitter',
    //             'value' => 'https://x.com/base-laravel',
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],
    //         [
    //             // Métadonnées SEO traduisibles
    //             'group' => 'seo',
    //             'key' => 'meta_title',
    //             'value' => null,
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => true,
    //             'translations' => [
    //                 ['lang_code' => 'fr', 'name' => 'Titre meta', 'value' => 'Base Laravel — Votre plateforme en ligne', 'description' => 'Le titre meta par défaut utilisé pour le référencement.'],
    //                 ['lang_code' => 'en', 'name' => 'Meta title', 'value' => 'Base Laravel — Your online platform', 'description' => 'The default meta title used for SEO.'],
    //             ],
    //         ],
    //         [
    //             'group' => 'seo',
    //             'key' => 'meta_description',
    //             'value' => null,
    //             'type' => 'string',
    //             'is_public' => true,
    //             'is_translatable' => true,
    //             'translations' => [
    //                 ['lang_code' => 'fr', 'name' => 'Description meta', 'value' => 'Bienvenue sur Base Laravel, votre plateforme en ligne.', 'description' => 'La description meta par défaut utilisée pour le référencement.'],
    //                 ['lang_code' => 'en', 'name' => 'Meta description', 'value' => 'Welcome to Base Laravel, your online platform.', 'description' => 'The default meta description used for SEO.'],
    //             ],
    //         ],
    //         [
    //             'group' => 'seo',
    //             'key' => 'google_analytics_id',
    //             'value' => 'G-XXXXXXXXXX',
    //             'type' => 'string',
    //             'is_public' => false,
    //             'is_translatable' => false,
    //             'translations' => [],
    //         ],

    //         // Nombre maximum de fichiers cloudfiles non synchronisés avant redirection
    //         [
    //             'group' => 'cloudfiles',
    //             'key' => 'max_unsynced_files_before_redirect',
    //             'value' => 10,
    //             'type' => 'integer',
    //             'is_public' => false,
    //             'is_translatable' => false,
    //             'translations' => [
    //                 ['lang_code' => 'fr', 'name' => 'Nombre maximum de fichiers non synchronisés avant redirection', 'value' => null, 'description' => 'Le nombre maximum de fichiers cloudfiles non synchronisés avant que l\'utilisateur soit redirigé vers la page de synchronisation.'],
    //                 ['lang_code' => 'en', 'name' => 'Maximum unsynced files before redirect', 'value' => null, 'description' => 'The maximum number of unsynced cloudfiles before the user is redirected to the sync page.'],
    //             ],
    //         ],
    //         // Nombres maximum de fichiers cloudfiles à synchroniser en une seule fois
    //         [
    //             'group' => 'cloudfiles',
    //             'key' => 'max_files_sync_batch',
    //             'value' => 10,
    //             'type' => 'integer',
    //             'is_public' => false,
    //             'is_translatable' => false,
    //             'translations' => [
    //                 ['lang_code' => 'fr', 'name' => 'Nombre maximum de fichiers à synchroniser en une seule fois', 'value' => null, 'description' => 'Le nombre maximum de fichiers cloudfiles à synchroniser en une seule opération.'],
    //                 ['lang_code' => 'en', 'name' => 'Maximum files to sync in one batch', 'value' => null, 'description' => 'The maximum number of cloudfiles to sync in a single operation.']
    //             ],
    //         ],
    //     ];

    //     // Boucle principale d’insertion / mise à jour des réglages.
    //     foreach ($settings as $data) {
    //         // Les traductions sont traitées séparément après la création ou mise à jour du setting principal.
    //         $translations = $data['translations'] ?? [];
    //         unset($data['translations']);

    //         // Crée le setting s’il n’existe pas, sinon le met à jour.
    //         $setting = Setting::updateOrCreate(
    //             ['group' => $data['group'], 'key' => $data['key']],
    //             [
    //                 'value' => $data['value'],
    //                 'type' => $data['type'],
    //                 'is_public' => $data['is_public'],
    //                 'is_translatable' => $data['is_translatable'],
    //             ]
    //         );

    //         // Ajoute ou met à jour les traductions associées à chaque setting.
    //         foreach ($translations as $translation) {
    //             $lang = Lang::where('code', $translation['lang_code'])->first();

    //             if (!$lang) {
    //                 $this->command->warn("Langue '{$translation['lang_code']}' non trouvée pour le setting '{$setting->key}'. Traduction ignorée.");

    //                 continue;
    //             }

    //             SettingTranslation::updateOrCreate(
    //                 ['setting_id' => $setting->id, 'lang_id' => $lang->id],
    //                 [
    //                     'name' => $translation['name'],
    //                     'value' => $translation['value'],
    //                     'description' => $translation['description'] ?? null,
    //                 ]
    //             );
    //         }
    //     }
    // }
}
