<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\BlogPostComment;
use App\Models\BlogPostCommentReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        if (!$user) {
            $this->command->error("Aucun utilisateur trouvé. Veuillez d'abord créer un utilisateur.\n");

            return;
        }

        $comments = [
            [
                'post_id' => 1, // IA et développeurs
                'content' => 'Article très intéressant ! Je pense que l\'IA va surtout nous aider à être plus productifs plutôt que de nous remplacer complètement.',
                'is_visible' => true,
                'replies' => [
                    ['content' => 'Tout à fait d\'accord ! Les développeurs qui maîtrisent l\'IA auront un avantage énorme sur le marché du travail.', 'is_visible' => true],
                ],
            ],
            [
                'post_id' => 1, // IA et développeurs
                'content' => 'Je suis un peu sceptique. Avec l\'évolution rapide de l\'IA, certains métiers techniques risquent quand même de disparaître à moyen terme.',
                'is_visible' => true,
                'replies' => [
                    ['content' => 'C\'est une crainte légitime, mais l\'histoire montre que chaque révolution technologique crée plus d\'emplois qu\'elle n\'en détruit.', 'is_visible' => true],
                ],
            ],
            [
                'post_id' => 2, // Bien-être
                'content' => 'Ces conseils sont vraiment utiles ! J\'ai commencé à me lever tôt depuis 2 semaines et je me sens déjà beaucoup mieux.',
                'is_visible' => true,
                'replies' => [
                    ['content' => 'Bravo pour cette démarche ! La régularité est la clé, continuez comme ça !', 'is_visible' => true],
                ],
            ],
            [
                'post_id' => 3, // Bénin voyage
                'content' => 'Ganvié est vraiment un endroit magique ! J\'y suis allé l\'année dernière et c\'était une expérience inoubliable.',
                'is_visible' => true,
                'replies' => [
                    ['content' => 'Je rêve d\'y aller ! Est-ce qu\'il y a une bonne période pour visiter le Bénin ?', 'is_visible' => false],
                ],
            ],
            [
                'post_id' => 4, // Apprendre à coder
                'content' => 'Super guide pour les débutants ! Je recommande aussi The Odin Project qui est une excellente ressource gratuite pour apprendre le développement web.',
                'is_visible' => true,
                'replies' => [
                    ['content' => 'Merci pour la suggestion ! The Odin Project est effectivement très complet, surtout pour JavaScript et Ruby.', 'is_visible' => true],
                ],
            ],
        ];

        foreach ($comments as $data) {

            $replies = $data['replies'];
            unset($data['replies']);

            // Créer le commentaire
            $comment = BlogPostComment::create([
                'post_id' => $data['post_id'],
                'user_id' => $user->id,
                'content' => $data['content'],
                'is_visible' => $data['is_visible'],
            ]);

            // Créer les réponses
            foreach ($replies as $reply) {
                BlogPostCommentReply::create([
                    'comment_id' => $comment->id,
                    'user_id' => $user->id,
                    'content' => $reply['content'],
                    'is_visible' => $reply['is_visible'],
                ]);
            }
        }
    }
}
