<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\Lang;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = [
            [
                'category_id' => 1, // Technologie
                'views' => 1240,
                'time_read' => 5,
                'status' => 'published',
                'sort_order' => 1,
                'published_at' => getDateTime(),
                'translations' => [
                    [
                        'lang_code' => 'fr',
                        'title' => 'L\'intelligence artificielle va-t-elle remplacer les développeurs ?',
                        'slug' => 'intelligence-artificielle-va-t-elle-remplacer-les-developpeurs',
                        'content' => '<p>L\'intelligence artificielle transforme profondément le monde du développement logiciel. Des outils comme GitHub Copilot ou ChatGPT permettent aujourd\'hui de générer du code en quelques secondes.</p><p>Cependant, la créativité, la compréhension des besoins métiers et la capacité à résoudre des problèmes complexes restent des compétences exclusivement humaines. L\'IA est un outil puissant, mais elle ne remplace pas le développeur — elle le rend plus efficace.</p><p>En conclusion, les développeurs qui sauront tirer parti de l\'IA seront les plus compétitifs sur le marché du travail de demain.</p>',
                    ],
                    [
                        'lang_code' => 'en',
                        'title' => 'Will Artificial Intelligence Replace Developers?',
                        'slug' => 'will-artificial-intelligence-replace-developers',
                        'content' => '<p>Artificial intelligence is profoundly transforming the world of software development. Tools like GitHub Copilot or ChatGPT can now generate code in seconds.</p><p>However, creativity, understanding business needs and the ability to solve complex problems remain exclusively human skills. AI is a powerful tool, but it does not replace the developer — it makes them more efficient.</p><p>In conclusion, developers who know how to leverage AI will be the most competitive in tomorrow\'s job market.</p>',
                    ],
                ],
            ],
            [
                'category_id' => 2, // Santé & Bien-être
                'views' => 875,
                'time_read' => 4,
                'status' => 'published',
                'sort_order' => 2,
                'published_at' => getDateTime(),
                'translations' => [
                    [
                        'lang_code' => 'fr',
                        'title' => '10 habitudes simples pour améliorer votre bien-être au quotidien',
                        'slug' => '10-habitudes-simples-pour-ameliorer-votre-bien-etre-au-quotidien',
                        'content' => '<p>Le bien-être ne s\'acquiert pas du jour au lendemain. Il résulte d\'un ensemble de petites habitudes adoptées chaque jour avec constance.</p><p>Parmi les habitudes les plus efficaces, on retrouve : se lever tôt, pratiquer 30 minutes d\'activité physique, boire suffisamment d\'eau, limiter les écrans avant de dormir et prendre le temps de méditer.</p><p>Ces gestes simples, répétés chaque jour, ont un impact considérable sur votre énergie, votre humeur et votre santé globale.</p>',
                    ],
                    [
                        'lang_code' => 'en',
                        'title' => '10 Simple Habits to Improve Your Daily Well-Being',
                        'slug' => '10-simple-habits-to-improve-your-daily-well-being',
                        'content' => '<p>Well-being is not achieved overnight. It results from a set of small habits adopted every day with consistency.</p><p>Among the most effective habits are: waking up early, doing 30 minutes of physical activity, drinking enough water, limiting screens before sleep and taking time to meditate.</p><p>These simple gestures, repeated every day, have a considerable impact on your energy, mood and overall health.</p>',
                    ],
                ],
            ],
            [
                'category_id' => 3, // Voyage
                'views' => 2100,
                'time_read' => 6,
                'status' => 'published',
                'sort_order' => 3,
                'published_at' => getDateTime(),
                'translations' => [
                    [
                        'lang_code' => 'fr',
                        'title' => 'Bénin : les 5 incontournables à visiter absolument',
                        'slug' => 'benin-les-5-incontournables-a-visiter-absolument',
                        'content' => '<p>Le Bénin est une destination fascinante, riche d\'une histoire millénaire et d\'une culture vibrante. Voici les 5 lieux à ne pas manquer lors de votre voyage.</p><p>1. <strong>Ouidah</strong> : berceau du vaudou et de la traite négrière. 2. <strong>Abomey</strong> : ancienne capitale du Royaume du Dahomey et ses palais royaux classés UNESCO. 3. <strong>Ganvié</strong> : le célèbre village lacustre surnommé la "Venise de l\'Afrique". 4. <strong>Cotonou</strong> : la capitale économique animée et son marché Dantokpa. 5. <strong>Pendjari</strong> : le parc national pour les amateurs de safari et de faune sauvage.</p><p>Le Bénin vous réserve une expérience humaine et culturelle inoubliable.</p>',
                    ],
                    [
                        'lang_code' => 'en',
                        'title' => 'Benin: The 5 Must-See Places to Visit',
                        'slug' => 'benin-the-5-must-see-places-to-visit',
                        'content' => '<p>Benin is a fascinating destination, rich in millennia of history and a vibrant culture. Here are the 5 places you must not miss during your trip.</p><p>1. <strong>Ouidah</strong>: the cradle of voodoo and the slave trade. 2. <strong>Abomey</strong>: former capital of the Kingdom of Dahomey with its UNESCO-listed royal palaces. 3. <strong>Ganvié</strong>: the famous lake village nicknamed the "Venice of Africa". 4. <strong>Cotonou</strong>: the bustling economic capital and its Dantokpa market. 5. <strong>Pendjari</strong>: the national park for safari and wildlife enthusiasts.</p><p>Benin promises an unforgettable human and cultural experience.</p>',
                    ],
                ],
            ],
            [
                'category_id' => 4, // Éducation
                'views' => 530,
                'time_read' => 7,
                'status' => 'published',
                'sort_order' => 4,
                'published_at' => getDateTime(),
                'translations' => [
                    [
                        'lang_code' => 'fr',
                        'title' => 'Comment apprendre à coder seul en 2025 : le guide complet',
                        'slug' => 'comment-apprendre-a-coder-seul-en-2025-guide-complet',
                        'content' => '<p>Apprendre à coder seul est aujourd\'hui possible grâce à la multitude de ressources disponibles en ligne. Mais par où commencer ?</p><p>La première étape est de choisir un langage adapté à vos objectifs : Python pour la data science, JavaScript pour le web, PHP pour le développement backend. Ensuite, suivez un plan d\'apprentissage structuré avec des plateformes comme OpenClassrooms, freeCodeCamp ou The Odin Project.</p><p>La clé du succès est la régularité : 1 heure par jour vaut mieux que 8 heures le weekend. Pratiquez sur des projets concrets dès le début pour ancrer vos connaissances.</p>',
                    ],
                    [
                        'lang_code' => 'en',
                        'title' => 'How to Learn to Code on Your Own in 2025: The Complete Guide',
                        'slug' => 'how-to-learn-to-code-on-your-own-in-2025-complete-guide',
                        'content' => '<p>Learning to code on your own is now possible thanks to the multitude of resources available online. But where do you start?</p><p>The first step is to choose a language suited to your goals: Python for data science, JavaScript for the web, PHP for backend development. Then follow a structured learning plan with platforms like freeCodeCamp, The Odin Project or Codecademy.</p><p>The key to success is consistency: 1 hour a day is better than 8 hours on the weekend. Practice on real projects from the start to consolidate your knowledge.</p>',
                    ],
                ],
            ],
            [
                'category_id' => 5, // Business
                'views' => 340,
                'time_read' => 5,
                'status' => 'draft',
                'sort_order' => 5,
                'published_at' => null,
                'translations' => [
                    [
                        'lang_code' => 'fr',
                        'title' => 'Lancer sa startup en Afrique : défis et opportunités en 2025',
                        'slug' => 'lancer-sa-startup-en-afrique-defis-et-opportunites-en-2025',
                        'content' => '<p>L\'Afrique est aujourd\'hui l\'un des continents les plus dynamiques en matière d\'entrepreneuriat. Des hubs comme Lagos, Nairobi, Accra et Cotonou voient émerger des startups innovantes chaque année.</p><p>Les opportunités sont immenses : une population jeune et connectée, des problématiques locales à résoudre, et un accès croissant au financement via des fonds comme CcHub, Partech Africa ou Orange Ventures.</p><p>Les défis restent cependant réels : accès limité au crédit, instabilité des devises, infrastructures parfois défaillantes. Mais les entrepreneurs africains font preuve d\'une résilience et d\'une créativité remarquables pour les surmonter.</p>',
                    ],
                    [
                        'lang_code' => 'en',
                        'title' => 'Launching a Startup in Africa: Challenges and Opportunities in 2025',
                        'slug' => 'launching-a-startup-in-africa-challenges-and-opportunities-in-2025',
                        'content' => '<p>Africa is today one of the most dynamic continents in terms of entrepreneurship. Hubs like Lagos, Nairobi, Accra and Cotonou see innovative startups emerging every year.</p><p>The opportunities are immense: a young and connected population, local problems to solve, and growing access to funding through funds like CcHub, Partech Africa or Orange Ventures.</p><p>However, challenges remain real: limited access to credit, currency instability, sometimes failing infrastructure. But African entrepreneurs show remarkable resilience and creativity in overcoming them.</p>',
                    ],
                ],
            ],
        ];

        // Récupérer le premier user existant
        $user = User::first();

        if (! $user) {
            $this->command->error("Aucun utilisateur trouvé. Veuillez d'abord créer un utilisateur.\n");

            return;
        }

        foreach ($posts as $data) {

            $translations = $data['translations'];
            unset($data['translations']);

            // Créer le post
            $post = BlogPost::create([
                'category_id' => $data['category_id'],
                'user_id' => $user->id,
                'views' => $data['views'],
                'time_read' => $data['time_read'],
                'status' => $data['status'],
                'sort_order' => $data['sort_order'],
                'published_at' => $data['published_at'],
            ]);

            // Créer les traductions
            foreach ($translations as $translation) {

                $lang = Lang::where('code', $translation['lang_code'])->where('is_active', 1)->first();

                if (! $lang) {
                    $this->command->error('Langue non trouvée pour le code : '.$translation['lang_code']."\n");

                    continue;
                }
                BlogPostTranslation::create([
                    'post_id' => $post->id,
                    'lang_id' => $lang->id,
                    'title' => $translation['title'],
                    'slug' => $translation['slug'],
                    'content' => $translation['content'],
                ]);
            }
        }

        $postTags = [
            // Post 1 (IA et développeurs) → tags: Intelligence Artificielle, Développement Web
            1 => [1, 2],

            // Post 2 (Bien-être) → tags: Développement Personnel
            2 => [4],

            // Post 3 (Bénin voyage) → tags: Environnement
            3 => [5],

            // Post 4 (Apprendre à coder) → tags: Développement Web, Développement Personnel
            4 => [2, 4],

            // Post 5 (Startup Afrique) → tags: Entrepreneuriat, Développement Personnel
            5 => [3, 4],
        ];

        foreach ($postTags as $postId => $tagIds) {
            $post = BlogPost::find($postId);

            if ($post) {
                $post->tags()->attach($tagIds);
            }
        }

    }
}
