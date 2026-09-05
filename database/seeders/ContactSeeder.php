<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'user_id' => null,
                'fullname' => 'Jean Dupont',
                'email' => 'jean.dupont@example.fr',
                'phone' => '+33 6 12 34 56 78',
                'subject' => 'Demande d\'information',
                'message' => 'Bonjour, je souhaiterais obtenir plus d\'informations sur vos services. Pourriez-vous me contacter dans les plus brefs délais ? Merci d\'avance.',
                'is_read' => true,
            ],
            [
                'user_id' => null,
                'fullname' => 'Marie Martin',
                'email' => 'marie.martin@example.fr',
                'phone' => null,
                'subject' => 'Problème technique',
                'message' => 'Bonjour, je rencontre un problème technique sur votre plateforme. La page de connexion ne répond plus depuis ce matin. Merci de prendre en charge ce problème rapidement.',
                'is_read' => false,
            ],
            [
                'user_id' => null,
                'fullname' => 'Amina Diallo',
                'email' => 'amina.diallo@example.bj',
                'phone' => '+229 97 12 34 56',
                'subject' => null,
                'message' => 'Bonjour, je voudrais savoir si vous proposez des formations en ligne. Je suis basée au Bénin et je suis très intéressée par vos contenus.',
                'is_read' => false,
            ],
            [
                'user_id' => null,
                'fullname' => 'Carlos Silva',
                'email' => 'carlos.silva@example.com',
                'phone' => '+55 11 98765 4321',
                'subject' => 'Partenariat commercial',
                'message' => 'Bonjour, je représente une société brésilienne spécialisée dans le développement web. Nous serions intéressés par un partenariat commercial avec votre organisation. Pouvez-vous nous contacter ?',
                'is_read' => true,
            ],
            [
                'user_id' => null,
                'fullname' => 'Sophie Leclerc',
                'email' => 'sophie.leclerc@example.fr',
                'phone' => '+33 7 98 76 54 32',
                'subject' => 'Suggestion d\'amélioration',
                'message' => 'Bonjour, j\'utilise votre plateforme depuis plusieurs mois et j\'aurais quelques suggestions d\'amélioration à vous soumettre, notamment sur l\'interface mobile qui pourrait être optimisée. Merci.',
                'is_read' => false,
            ],
        ];

        foreach ($contacts as $data) {

            Contact::create([
                'user_id' => $data['user_id'],
                'fullname' => $data['fullname'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'subject' => $data['subject'],
                'message' => $data['message'],
                'is_read' => $data['is_read'],
            ]);
        }
    }
}
