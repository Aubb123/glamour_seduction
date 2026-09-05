<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

// ✅ Tu dois importer TES modèles custom
use App\Models\Permission;
use App\Models\Role;
// use Spatie\Permission\Models\Permission;
// use Spatie\Permission\Models\Role;

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    // Actions par défaut pour chaque modèle (index, create, store, show, edit, update, destroy)
    protected array $defaultActions = [
        'index', 'create', 'store', 'show', 'edit', 'update', 'destroy', 'trashed', 'restore', 'force_destroy',
    ];

    // Définition des modèles, actions et permissions à créer
    protected array $models = [
        'langs' => ['activate', 'deactivate'], // Exemple pour les langues, avec des actions spécifiques en plus du CRUD de base
        'platform-versions' => ['activate', 'deactivate', 'publish', 'unpublish', 'audits', 'activity_logs'], // Exemple pour les versions du site, avec des actions spécifiques en plus du CRUD de base
        'platform-version-translations' => [], // Exemple pour les traductions des versions, avec uniquement les actions de base
        'geo-continents' => ['activate', 'deactivate', 'audits', 'activity_logs'], // Exemple pour les continents, avec des actions spécifiques en plus du CRUD de base
        'geo-continents-translations' => [], // Exemple pour les traductions des continents, avec uniquement les actions de base
        'geo-countries' => ['activate', 'deactivate', 'audits', 'activity_logs'], // Exemple pour les pays, avec des actions spécifiques en plus du CRUD de base
        'geo-countries-translations' => [], // Exemple pour les traductions des pays, avec uniquement les actions de base
        'roles' => ['activate', 'deactivate', 'audits', 'activity_logs'], // Exemple pour les rôles, avec des actions spécifiques en plus du CRUD de base
        'roles-translations' => [], // Exemple pour les traductions des rôles, avec uniquement les actions de base
        'permissions' => ['activate', 'deactivate', 'assign', 'sync_roles', 'audits', 'activity_logs'], // Exemple pour les permissions, avec des actions spécifiques en plus du CRUD de base
        'blog-categories' => ['activate', 'deactivate', 'audits', 'activity_logs'], // Catégories de blog
        'blog-category-translations' => [], // Exemple pour les traductions des catégories de blog, avec uniquement les actions de base
        'blog-tags' => ['activate', 'deactivate', 'audits', 'activity_logs'], // Tags de blog
        'blog-tag-translations' => [], // Exemple pour les traductions des tags de blog, avec uniquement les actions de base
        'blog-posts' => ['toggle_visibility', 'audits', 'activity_logs'], // Articles de blog
        'blog-post-translations' => [], // Exemple pour les traductions des articles de blog
        'blog-post-comments' => ['activate', 'deactivate', 'audits', 'activity_logs', 'replies'], // Commentaires de blog
        'blog-post-comment-replies' => ['activate', 'deactivate', 'audits', 'activity_logs'], // Réponses aux commentaires de blog
        'galleries' => ['toggle_active', 'audits', 'activity_logs'], // Galeries
        'gallery-translations' => [], // Traductions des galeries
        'faqs' => ['toggle_active', 'audits', 'activity_logs'], // FAQs
        'faq-translations' => [], // Traductions des FAQs
        'subscribes' => ['activate', 'deactivate', 'audits', 'activity_logs'], // Abonnés newsletter
        'subscribe-histories' => [], // Historique des abonnements
        'cloud-platforms' => ['toggle_active', 'architecture_store', 'audits', 'activity_logs'], // Plateformes cloud
        'cloud-folders' => ['toggle_active', 'audits', 'activity_logs'], // Dossiers cloud
        'cloud-files' => ['audits', 'activity_logs'], // Fichiers cloud
        'contacts' => ['mark_as_read', 'mark_as_unread', 'audits', 'activity_logs'], // Messages de contact
        'users' => ['activate', 'deactivate', 'suspend', 'ban', 'activity_logs', 'subscribes', 'galleries', 'contacts', 'faqs', 'audits', 'blog_posts', 'blog_post_comments', 'blog_post_comment_replies'], // Exemple pour les utilisateurs, avec des actions spécifiques en plus du CRUD de base
        'audits' => ['clear'], // Audits (journal d'activité)
        'activity-logs' => ['clear'], // Journaux d'activité
        'settings' => ['update', 'audits', 'activity_logs'], // Paramètres (settings)
        // Ajoute d'autres modèles ici avec leurs actions spécifiques si nécessaire
    ];

    // ✅ Tu ajoutes ça ici
    protected array $rolePermissions = [
        'administrator' => '*',
        'developer' => '*',
        'moderator' => [
            // langs
            'langs.index',

            // platform-versions
            'platform-versions.index',
            'platform-versions.create',
            'platform-versions.store',
            'platform-versions.show',
            'platform-versions.edit',
            'platform-versions.update',
            // platform-version-translations (index, create, store, show, edit, update)

            // geo-continents
            'geo-continents.index',
            'geo-continents.show',
            'geo-continents.edit',
            'geo-continents.update',
            // geo-continents-translations (index, create, store, show, edit, update)

            // geo-countries
            'geo-countries.index',
            'geo-countries.show',
            'geo-countries.edit',
            'geo-countries.update',
            // geo-countries-translations (index, create, store, show, edit, update)

            // roles
            'roles.index',
            'roles.show',
            // roles-translations (index, create, store, show, edit, update)

            // permissions
            'permissions.index',
            'permissions.show',
            'permissions.create',
            'permissions.store',
            'permissions.edit',
            'permissions.update',

            // blog-categories
            'blog-categories.index',
            'blog-categories.show',
            'blog-categories.create',
            'blog-categories.store',
            'blog-categories.edit',
            'blog-categories.update',
            // blog-category-translations (index, create, store, show, edit, update)

            // blog-tags
            'blog-tags.index',
            'blog-tags.show',
            'blog-tags.create',
            'blog-tags.store',
            'blog-tags.edit',
            'blog-tags.update',
            // blog-tag-translations (index, create, store, show, edit, update)

            // blog-posts
            'blog-posts.index',
            'blog-posts.show',
            'blog-posts.create',
            'blog-posts.store',
            'blog-posts.edit',
            'blog-posts.update',
            // blog-post-translations (index, create, store, show, edit, update)

            // users
            'users.index',
            'users.show',
            'users.create',
            'users.store',
            'users.edit',
            'users.update',

        ],
        'support' => [
            // platform-versions
            'platform-versions.index',
            'platform-versions.show',
            // platform-version-translations
            'platform-version-translations.index',
            'platform-version-translations.show',
        ],
        'user' => [],
    ];

    public function run(): void
    {
        // 1. Créer toutes les permissions
        $allPermissions = [];

        // Parcours de chaque modèle et de ses actions pour créer les permissions
        foreach ($this->models as $model => $extraActions) {

            // Fusionne les actions par défaut avec les actions spécifiques au modèle et crée les permissions
            // (ex: platform-versions.index, platform-versions.publish, etc.Et ajoute-les à la liste de toutes les permissions)
            $actions = array_merge($this->defaultActions, $extraActions);

            // Crée la permission pour chaque action du modèle (ex: platform-versions.index, platform-versions.publish, etc.) et ajoute-la à la liste de toutes les permissions
            foreach ($actions as $action) {
                $perm = Permission::firstOrCreate([
                    'name' => "{$model}.{$action}",
                    'guard_name' => 'web', // guard_name par défaut
                ]); // Crée la permission si elle n'existe pas déjà
                $allPermissions[] = $perm->name; // Ajoute le nom de la permission à la liste de toutes les permissions
            }
        }

        // 2. Assigner les permissions aux rôles
        foreach ($this->rolePermissions as $roleName => $permissions) {

            // Récupère le rôle par son nom (ex: administrator, developer, etc.)
            $role = Role::findByName($roleName);

            if (!$role) {
                // Log un avertissement si le rôle n'existe pas
                $this->command->warn("Role '{$roleName}' not found, skipping permission assignment.");

                continue;
            } // Si le rôle n'existe pas, passe au suivant

            // Si les permissions sont définies sur '*', assigne toutes les permissions au rôle, sinon assigne uniquement les permissions spécifiées pour ce rôle
            if ($permissions === '*') {
                $role->givePermissionTo($allPermissions);
                $this->command->info("Assigned all permissions to role '{$roleName}'.");
            } else {
                // Filtrer les permissions qui existent réellement
                $validPermissions = array_intersect($permissions, $allPermissions);
                $invalidPermissions = array_diff($permissions, $validPermissions);

                if (! empty($invalidPermissions)) {
                    $this->command->warn("Role '{$roleName}': The following permissions do not exist and were skipped: ".implode(', ', $invalidPermissions));
                }

                if (! empty($validPermissions)) {
                    $role->givePermissionTo($validPermissions);
                    $this->command->info('Assigned '.count($validPermissions)." permissions to role '{$roleName}'.");
                }
            }
        }

        // 3. Nettoyer les permissions orphelines (non assignées à aucun rôle)
        $orphanPermissions = Permission::whereDoesntHave('roles')->get();
        if ($orphanPermissions->count() > 0) {
            $this->command->warn("Found {$orphanPermissions->count()} orphan permissions (not assigned to any role):");
            foreach ($orphanPermissions as $perm) {
                $this->command->warn("  - {$perm->name}");
            }
        }
    }
}
