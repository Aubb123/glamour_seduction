<?php

namespace App\Services\V1\Controllers;

use App\Enums\Blogs\Posts\BlogPostStatus;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use function __;

class BlogPostService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/blogs/posts/messages/posts-messages-service.posts';

    // Chemin des messages génériques du backend helper (factorisés pour toutes les entités traduisibles)
    protected string $backendHelperLangPath = 'v1/backend/others/helpers/backend-helper-messages';

    public function __construct(
        protected CloudFileService $cloudFileService,
    ) {}

    // ─────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────
    public function backend_blog_posts_store(array $data): BlogPost
    {
        return DB::transaction(function () use ($data) {

            // Gestion du statut
            $status = BlogPostStatus::tryFrom($data['status'] ?? '') ?? BlogPostStatus::Draft;

            // Validation métier du statut programmé
            $this->validateScheduledStatus($status, $data['published_at'] ?? null);

            // Calcul automatique du temps de lecture depuis le contenu (le système calcule toujours)
            $timeRead = $this->calculateTimeRead($this->getFirstTranslationContent($data['translations'] ?? []));

            // Gestion de published_at selon le statut (logique centralisée)
            $publishedAt = $this->resolvePublishedAt($status, $data['published_at'] ?? null);

            $post = BlogPost::create([
                'category_id' => $data['category_id'],
                'user_id' => auth()->id(),
                'views' => $data['views'] ?? 0,
                'time_read' => $timeRead,
                'status' => $status,
                'sort_order' => $data['sort_order'],
                'published_at' => $publishedAt,
            ]);

            // Sync des tags si présents
            if (! empty($data['tags'])) {
                $post->tags()->sync($data['tags']);
            }

            // Création des traductions
            $this->syncTranslations($post, $data['translations']);

            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.store.cloud_platform_uuid_required"));
                }

                // Appel du service pour gérer le téléchargement de l'image vers le cloud ou le stockage local, en associant l'image à la publication créée
                $this->cloudFileService->uploadEntityImage(
                    $data['image'],
                    $data['cloud_platform_uuid'],
                    $post,
                    $post->getTable(),
                    $this->backendLangPath,
                );
            }

            return $post;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_blog_posts_update(BlogPost $post, array $data): BlogPost
    {
        return DB::transaction(function () use ($post, $data) {

            $updateData = [];

            if (isset($data['category_id'])) {
                $updateData['category_id'] = $data['category_id'];
            }

            // Le temps de lecture est toujours calculé automatiquement par le système
            if (isset($data['translations'])) {
                // Recalcule le temps de lecture depuis le contenu mis à jour
                $updateData['time_read'] = $this->calculateTimeRead($this->getFirstTranslationContent($data['translations']));
            }

            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            // Gestion du statut
            if (isset($data['status'])) {

                // Conversion du statut en enum, avec fallback sur Draft si invalide
                $status = BlogPostStatus::tryFrom($data['status']) ?? BlogPostStatus::Draft;

                // Validation métier du statut programmé
                $this->validateScheduledStatus($status, $data['published_at'] ?? null);

                $updateData['status'] = $status;

                // Gestion de published_at selon le statut (logique centralisée)
                $updateData['published_at'] = $this->resolvePublishedAt($status, $data['published_at'] ?? null, $post);

            }

            // Mise à jour du post si des données sont présentes
            if (! empty($updateData)) {
                $post->update($updateData);
            }

            // Sync des tags si présents
            if (isset($data['tags'])) {
                $post->tags()->sync($data['tags']);
            }

            // Mise à jour des traductions
            if (isset($data['translations'])) {
                $this->syncTranslations($post, $data['translations']);
            }

            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.cloud_platform_uuid_required"));
                }

                // Appel du service pour gérer le téléchargement de l'image vers le cloud ou le stockage local, en associant l'image à la publication mise à jour
                $this->cloudFileService->uploadEntityImage(
                    $data['image'],
                    $data['cloud_platform_uuid'],
                    $post,
                    $post->getTable(),
                    $this->backendLangPath,
                );
            }

            return $post;
        });
    }

    // ─────────────────────────────────────────────
    // CHANGE STATUS
    // ─────────────────────────────────────────────
    public function backend_blog_posts_change_status(BlogPost $post, BlogPostStatus $newStatus, ?string $publishedAt = null): void
    {
        DB::transaction(function () use ($post, $newStatus, $publishedAt) {

            // Validation métier du statut programmé
            $this->validateScheduledStatus($newStatus, $publishedAt);

            $updateData = ['status' => $newStatus];

            // Gestion de published_at selon le statut (logique centralisée)
            $updateData['published_at'] = $this->resolvePublishedAt($newStatus, $publishedAt, $post);

            $post->update($updateData);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_blog_posts_destroy(BlogPost $post): void
    {
        DB::transaction(function () use ($post) {

            // Vérification : empêcher la suppression d'un post publié
            if ($post->status === BlogPostStatus::Published) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.published_forbidden"));
            }

            if ($post->trashed()) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.destroy.already_deleted"));
            }

            destroy_function($post);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_posts_restore(BlogPost $post): void
    {
        DB::transaction(function () use ($post) {
            if ($post->trashed()) {
                restore_function($post);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_posts_force_destroy(BlogPost $post): void
    {
        DB::transaction(function () use ($post) {
            if ($post->trashed()) {
                // Une entité active ne peut pas être supprimée définitivement
                if ($post->status === BlogPostStatus::Published) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.active_forbidden"));
                }
                force_destroy_function($post);
            } else {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // RESOLVE PUBLISHED AT (privé)
    // ─────────────────────────────────────────────
    /**
     * Détermine la valeur de published_at selon le statut du post.
     *
     * - draft      → null (jamais de date)
     * - published  → conserve la date existante si elle n'est pas future, sinon now()
     * - archived   → conserve la date de publication initiale (inchangée)
     * - scheduled  → date fournie obligatoire (validée dans le futur)
     */
    private function resolvePublishedAt(BlogPostStatus $status, ?string $publishedAt = null, ?BlogPost $post = null): ?Carbon
    {
        return match ($status) {
            // Un passage à 'published' ne doit jamais conserver une date future (sinon le
            // scopePublished [published_at <= now()] rendrait le post invisible). On force
            // donc now() si la date existante est dans le futur.
            BlogPostStatus::Published => $post?->published_at?->isFuture() ? getDateTime() : ($post?->published_at ?? getDateTime()),
            BlogPostStatus::Archived => $post?->published_at,
            BlogPostStatus::Scheduled => $publishedAt ? Carbon::parse($publishedAt) : null,
            default => null,
        };
    }

    // ─────────────────────────────────────────────
    // VALIDATE SCHEDULED STATUS (privé)
    // ─────────────────────────────────────────────
    private function validateScheduledStatus(BlogPostStatus $status, ?string $publishedAt = null): void
    {
        if ($status !== BlogPostStatus::Scheduled) {
            return;
        }

        if (empty($publishedAt)) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.scheduled_date_required"));
        }

        if (Carbon::parse($publishedAt)->isPast()) {
            throw new \InvalidArgumentException(__("{$this->backendLangPath}.scheduled_date_past"));
        }
    }

    // ─────────────────────────────────────────────
    // CALCULATE TIME READ (privé)
    // ─────────────────────────────────────────────
    private function calculateTimeRead(string $content): int
    {
        // ~200 mots par minute (vitesse de lecture moyenne)
        $wordCount = str_word_count(strip_tags($content));
        $minutes = (int) ceil($wordCount / 200);

        return max(1, $minutes); // minimum 1 minute
    }

    // ─────────────────────────────────────────────
    // GET FIRST TRANSLATION CONTENT (privé)
    // ─────────────────────────────────────────────
    private function getFirstTranslationContent(array $translations): string
    {
        // Les traductions peuvent être indexées par code de langue (ex: translations[fr][content])
        // ou par index numérique (ex: translations[0]['content'])
        foreach ($translations as $translation) {
            if (! empty($translation['content'])) {
                return $translation['content'];
            }
        }

        return '';
    }

    // ─────────────────────────────────────────────
    // SYNC TRANSLATIONS (privé)
    // ─────────────────────────────────────────────
    private function syncTranslations(BlogPost $post, array $translations): void
    {
        // Récupère les IDs des langues soumises
        $submittedLangIds = collect($translations)->pluck('lang_id')->toArray();

        // Supprime les traductions qui ne sont pas présentes dans les données soumises
        BlogPostTranslation::where('post_id', $post->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->delete();
        // Suppression définitive des traductions supprimées (soft delete) qui sont en corbeille, supprimer précédemment
        BlogPostTranslation::onlyTrashed()->where('post_id', $post->id)
            ->whereNotIn('lang_id', $submittedLangIds)
            ->forceDelete();

        // Validation générique factorisée (BackendHelper) :
        //  - conflit titre/slug avec un autre article (actif ou en corbeille) dans la même langue ;
        //  - conflit avec la propre traduction de l'article, placée en corbeille.
        // Lève une InvalidArgumentException avec un message explicite si besoin.
        assertTranslationUniqueness(
            $post,
            $translations,
            __('v1/backend/others/helpers/backend-helper-messages.entityLabels.post'),
            $this->backendHelperLangPath,
            'title',
            'slug'
        );

        foreach ($translations as $translation) {

            // Vérifie si une traduction existe déjà pour le post et la langue
            $existingTranslation = BlogPostTranslation::withTrashed()
                ->where('post_id', $post->id)
                ->where('lang_id', $translation['lang_id'])
                ->first();

            // Si une traduction existante est trouvée
            if ($existingTranslation) {

                // Une traduction en corbeille ne doit pas être modifiée silencieusement
                if ($existingTranslation->trashed()) {
                    throw new \InvalidArgumentException(
                        __("{$this->backendLangPath}.trashedTranslationConflict", [
                            'lang_id' => $translation['lang_id'],
                        ])
                    );
                }

                // Met à jour la traduction existante
                $existingTranslation->update([
                    'title' => $translation['title'],
                    'content' => clean($translation['content']), // Nettoyage du contenu HTML pour éviter les scripts malveillants
                ]);

            } else {
                // Sinon, on crée une nouvelle traduction
                BlogPostTranslation::create([
                    'post_id' => $post->id,
                    'lang_id' => $translation['lang_id'],
                    'title' => $translation['title'],
                    'content' => clean($translation['content']),
                ]);
            }
        }
    }
}
