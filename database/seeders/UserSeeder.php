<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // -----------------------------------------------------------------------
        // Rôles utilisés (basés sur la table role_translations)
        // -----------------------------------------------------------------------

        $users = [

            // -------------------------
            // Administrateur (role_id = 1)
            // -------------------------
            [
                // 'role_id'           => 1,
                'country_id' => 1,
                'first_name' => 'Alice',
                'last_name' => 'Martin',
                'email' => 'admin@site.com',
                'gender' => 'female',
                'phone' => '+33 6 12 34 56 78',
                'phone_verified_at' => getDateTime(),
                'email_verified_at' => getDateTime(),
                'password' => Hash::make('password'),
                'status' => 'active',
            ],

            // -------------------------
            // Développeur (role_id = 2)
            // -------------------------
            [
                // 'role_id'           => 2,
                'country_id' => 1,
                'first_name' => 'Thomas',
                'last_name' => 'Dupont',
                'email' => 'developer@site.com',
                'gender' => 'male',
                'phone' => '+33 6 23 45 67 89',
                'phone_verified_at' => getDateTime(),
                'email_verified_at' => getDateTime(),
                'password' => Hash::make('password'),
                'status' => 'active',
            ],

            // -------------------------
            // Modérateur (role_id = 3)
            // -------------------------
            [
                // 'role_id'           => 3,
                'country_id' => 1,
                'first_name' => 'Hugo',
                'last_name' => 'Petit',
                'email' => 'moderator@site.com',
                'gender' => 'male',
                'phone' => '+33 6 34 56 78 90',
                'phone_verified_at' => getDateTime(),
                'email_verified_at' => getDateTime(),
                'password' => Hash::make('password'),
                'status' => 'active',
            ],

            // -------------------------
            // Support (role_id = 9)
            // -------------------------
            [
                // 'role_id'           => 9,
                'country_id' => 1,
                'first_name' => 'Camille',
                'last_name' => 'Simon',
                'email' => 'support@site.com',
                'gender' => 'female',
                'phone' => '+33 6 45 67 89 01',
                'phone_verified_at' => getDateTime(),
                'email_verified_at' => getDateTime(),
                'password' => Hash::make('password'),
                'status' => 'active',
            ],

            // -------------------------
            // Utilisateur (role_id = 10)
            // -------------------------
            [
                // 'role_id'           => 10,
                'country_id' => 1,
                'first_name' => 'Léa',
                'last_name' => 'Fontaine',
                'email' => 'user@site.com',
                'gender' => 'female',
                'phone' => '+33 6 56 78 90 12',
                'phone_verified_at' => getDateTime(),
                'email_verified_at' => getDateTime(),
                'password' => Hash::make('password'),
                'status' => 'active',
            ],

        ];

        // -----------------------------------------------------------------------
        // Assignation des rôles Spatie (après création des utilisateurs)
        // -----------------------------------------------------------------------
        $roleMap = [
            'admin@site.com' => 'administrator',
            'developer@site.com' => 'developer',
            'moderator@site.com' => 'moderator',
            'support@site.com' => 'support',
            'user@site.com' => 'user',
        ];

        // -----------------------------------------------------------------------
        // Insertion
        // -----------------------------------------------------------------------
        foreach ($users as $data) {
            $user = User::create(
                [
                    // // 'role_id'   => $data['role_id'],
                    'country_id' => $data['country_id'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'gender' => $data['gender'],
                    'phone' => $data['phone'],
                    'phone_verified_at' => $data['phone_verified_at'],
                    'email_verified_at' => $data['email_verified_at'],
                    'password' => $data['password'],
                    'status' => $data['status'],
                ]
            );

            // ✅ Assigner le rôle Spatie
            if (isset($roleMap[$data['email']])) {
                $user->assignRole($roleMap[$data['email']]);
            }

            // $this->command->info("✅ Utilisateur [{$data['email']}] inséré.");
        }

        // $this->command->info("🎉 UserSeeder terminé — " . count($users) . " utilisateurs insérés.");

    }
}
