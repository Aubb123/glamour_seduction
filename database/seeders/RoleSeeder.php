<?php

namespace Database\Seeders;

use App\Models\Lang;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RoleTranslation;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------------------------------------------------
        // Rôles à créer (basés sur la table role_translations)
        // -----------------------------------------------------------------------
        $roles = [

            // Role N°1 : Administrateur
            [
                'name' => 'administrator', // ← requis par Spatie (slug unique)
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'slug' => 'administrateur', 'label' => 'Administrateur', 'description' => 'Le rôle est attribué aux administrateurs du site, leur permettant de gérer les utilisateurs, les rôles, les permissions et d\'administrer le contenu du site.'],
                    ['lang_code' => 'en', 'slug' => 'administrator', 'label' => 'Administrator',  'description' => 'The role is assigned to site administrators, allowing them to manage users, roles, permissions and administer the site\'s content.'],
                ],
            ],

            // Role N°2 : Développeur
            [
                'name' => 'developer',
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'slug' => 'developpeur', 'label' => 'Développeur', 'description' => 'Le rôle est attribué aux développeurs du site, leur permettant de gérer le code, les fonctionnalités et les performances du site.'],
                    ['lang_code' => 'en', 'slug' => 'developer', 'label' => 'Developer',   'description' => 'The role is assigned to site developers, allowing them to manage the code, features and performance of the site.'],
                ],
            ],

            // Role N°3 : Modérateur
            [
                'name' => 'moderator',
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'slug' => 'moderateur', 'label' => 'Modérateur', 'description' => 'Le rôle est attribué aux modérateurs du site, leur permettant de gérer les contenus et les utilisateurs de manière approfondie.'],
                    ['lang_code' => 'en', 'slug' => 'moderator', 'label' => 'Moderator',  'description' => 'The role is assigned to site moderators, allowing them to manage content and users in depth.'],
                ],
            ],

            // Role N°4 : (à définir)
            [
                'name' => 'role-4',
                'status' => 'active',
                'translations' => [],  // ← vide, sera ajouté plus tard
            ],

            // Role N°5 : (à définir)
            [
                'name' => 'role-5',
                'status' => 'active',
                'translations' => [],
            ],

            // Role N°6 : (à définir)
            [
                'name' => 'role-6',
                'status' => 'active',
                'translations' => [],
            ],

            // Role N°7 : (à définir)
            [
                'name' => 'role-7',
                'status' => 'active',
                'translations' => [],
            ],

            // Role N°8 : (à définir)
            [
                'name' => 'role-8',
                'status' => 'active',
                'translations' => [],
            ],

            // Role N°9 : Support
            [
                'name' => 'support',
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'slug' => 'support', 'label' => 'Support', 'description' => 'Le rôle est attribué aux membres de l\'équipe de support, leur permettant de répondre aux demandes des utilisateurs.'],
                    ['lang_code' => 'en', 'slug' => 'support', 'label' => 'Support', 'description' => 'The role is assigned to members of the support team, allowing them to respond to user requests and provide technical assistance.'],
                ],
            ],

            // Role N°10 : Utilisateur
            [
                'name' => 'user',
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'slug' => 'utilisateur', 'label' => 'Utilisateur', 'description' => 'Le rôle est attribué aux visiteurs ou aux utilisateurs du site, leur permettant de consulter le contenu sans avoir accès aux fonctionnalités d\'administration.'],
                    ['lang_code' => 'en', 'slug' => 'user', 'label' => 'User', 'description' => 'The role is assigned to visitors or users of the site, allowing them to browse content without access to administration features.'],
                ],
            ],

        ];

        // -----------------------------------------------------------------------
        // Création des rôles et de leurs traductions
        // -----------------------------------------------------------------------
        foreach ($roles as $index => $data) {

            // Création du rôle Spatie (obligatoire pour la gestion des permissions)
            $role = Role::create([
                'name' => $data['name'],       // ← requis Spatie
                'guard_name' => 'web',               // ← requis Spatie
                'status' => $data['status'],
            ]);

            // Création des traductions (basées sur la table role_translations)
            foreach ($data['translations'] as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->first();
                if (! $lang) {
                    $this->command->error("❌ Langue '{$translation['lang_code']}' non trouvée pour le rôle N°".($index + 1).'. Traduction ignorée.');

                    continue;
                }

                // Création de la traduction du rôle
                RoleTranslation::create([
                    'role_id' => $role->id,
                    'lang_id' => $lang->id,
                    'slug' => $translation['slug'],
                    'label' => $translation['label'],
                    'description' => $translation['description'],
                ]);
            }

            // Assigner toutes les permissions au rôle administrator
            if ($data['name'] === 'administrator') {
                $role->givePermissionTo(Permission::all());
            }

        }
    }
}
