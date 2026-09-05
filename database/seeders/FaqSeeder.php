<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Faq;
use App\Models\FaqTranslation;
use App\Models\Lang;
use App\Models\User;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'sort_order' => 1,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'question' => 'Comment créer un compte ?',                     'answer' => 'Pour créer un compte, cliquez sur le bouton "S\'inscrire" en haut à droite de la page et remplissez le formulaire d\'inscription.'],
                    ['lang_code' => 'en', 'question' => 'How do I create an account?',                   'answer' => 'To create an account, click the "Sign Up" button at the top right of the page and fill in the registration form.'],
                ],
            ],
            [
                'sort_order' => 2,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'question' => 'Comment réinitialiser mon mot de passe ?',      'answer' => 'Cliquez sur "Mot de passe oublié" sur la page de connexion, entrez votre adresse e-mail et suivez les instructions envoyées par e-mail.'],
                    ['lang_code' => 'en', 'question' => 'How do I reset my password?',                   'answer' => 'Click "Forgot Password" on the login page, enter your email address and follow the instructions sent to your email.'],
                ],
            ],
            [
                'sort_order' => 3,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'question' => 'Comment contacter le support ?',                'answer' => 'Vous pouvez contacter notre équipe de support via le formulaire de contact disponible sur la page "Nous contacter" ou par e-mail à support@example.com.'],
                    ['lang_code' => 'en', 'question' => 'How do I contact support?',                     'answer' => 'You can contact our support team via the contact form available on the "Contact Us" page or by email at support@example.com.'],
                ],
            ],
            [
                'sort_order' => 4,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'question' => 'Comment supprimer mon compte ?',                'answer' => 'Pour supprimer votre compte, rendez-vous dans les paramètres de votre profil, section "Compte", puis cliquez sur "Supprimer mon compte". Cette action est irréversible.'],
                    ['lang_code' => 'en', 'question' => 'How do I delete my account?',                   'answer' => 'To delete your account, go to your profile settings, under the "Account" section, then click "Delete my account". This action is irreversible.'],
                ],
            ],
            [
                'sort_order' => 5,
                'status' => 'active',
                'translations' => [
                    ['lang_code' => 'fr', 'question' => 'Quels sont les moyens de paiement acceptés ?',  'answer' => 'Nous acceptons les paiements par carte bancaire (Visa, Mastercard), PayPal, ainsi que les virements bancaires pour les commandes professionnelles.'],
                    ['lang_code' => 'en', 'question' => 'What payment methods are accepted?',            'answer' => 'We accept payments by credit card (Visa, Mastercard), PayPal, as well as bank transfers for professional orders.'],
                ],
            ],
        ];

        // Utilisateur de référence (le premier de la base) pour les FAQs de démonstration
        $defaultUser = User::firstOrFail();

        foreach ($faqs as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer la FAQ
            $faq = Faq::create([
                'user_id' => $defaultUser->id,
                'sort_order' => $data['sort_order'],
                'status' => $data['status'],
            ]);

            // Créer les traductions
            foreach ($translations as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }

                FaqTranslation::create([
                    'faq_id' => $faq->id,
                    'lang_id' => $lang->id,
                    'question' => $translation['question'],
                    'answer' => $translation['answer'],
                ]);
            }
        }
    }
}
