<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Services\V1\Controllers\BlogCategoryTranslationService;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use function __;

class BlogCategoryTranslationController extends Controller
{
    use LogActivity;

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/blogs/categories/messages/categories-messages-controller.categories.translations';

    public function __construct(
        protected BlogCategoryTranslationService $blogCategoryTranslationService
    ) {}

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_category_translations_destroy(string $uuid): RedirectResponse
    {
        try {

            $translation = BlogCategoryTranslation::where('uuid', $uuid)->firstOrFail();

            // Suppression via le service (soft delete)
            $this->blogCategoryTranslationService->backend_blog_category_translations_destroy($translation);

            $this->logActivity(type: 'warning', action: 'backend.blog-category-translations.destroy', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.destroy.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-category-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-category-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-category-translations.destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.destroy.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // TRASHED
    // ─────────────────────────────────────────────
    public function backend_blog_category_translations_trashed(string $category_uuid): View
    {
        try {

            $category = BlogCategory::withTrashed()->where('uuid', $category_uuid)->firstOrFail();

            $trashedTranslations = BlogCategoryTranslation::onlyTrashed()->where('category_id', $category->id)->get();

            $this->logActivity(type: 'info', action: 'backend.blog-category-translations.trashed', context: ['count' => $trashedTranslations->count(), 'category_uuid' => $category_uuid], message: null, activity: __("{$this->langPath}.trashed.accessed", ['name' => $category->active_translation?->name ?? '']), is_success: true);

            return view('v1.backend.pages.blogs.categories.translations.trashed', compact('trashedTranslations', 'category'));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-category-translations.trashed', context: ['category_uuid' => $category_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.not_found", ['uuid' => $category_uuid]), is_success: false);

            abort(404);

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-category-translations.trashed', context: ['category_uuid' => $category_uuid], message: $e->getMessage(), activity: __("{$this->langPath}.trashed.error", ['uuid' => $category_uuid]), is_success: false);

            return abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_blog_category_translations_restore(string $uuid): RedirectResponse
    {
        try {

            $translation = BlogCategoryTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Restauration via le service
            $this->blogCategoryTranslationService->backend_blog_category_translations_restore($translation);

            $this->logActivity(type: 'info', action: 'backend.blog-category-translations.restore', context: ['translation_uuid' => $translation->uuid], message: null, activity: __("{$this->langPath}.restore.success", ['uuid' => $translation->uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.restore.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-category-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-category-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-category-translations.restore', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.restore.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.restore.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_blog_category_translations_force_destroy(string $uuid): RedirectResponse
    {
        try {

            // Récupération de la traduction supprimée
            $translation = BlogCategoryTranslation::onlyTrashed()->where('uuid', $uuid)->firstOrFail();

            // Suppression définitive via le service
            $this->blogCategoryTranslationService->backend_blog_category_translations_force_destroy($translation);

            // Log de l'activité
            $this->logActivity(type: 'info', action: 'backend.blog-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: null, activity: __("{$this->langPath}.force_destroy.success", ['uuid' => $uuid]), is_success: true, loggable: $translation);

            return back()->with('success', __("{$this->langPath}.force_destroy.flash_success"));

        } catch (ModelNotFoundException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_warning"));

        } catch (\InvalidArgumentException $e) {

            $this->logActivity(type: 'warning', action: 'backend.blog-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.warning_meta", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', $e->getMessage());

        } catch (\Exception $e) {

            $this->logActivity(type: 'error', action: 'backend.blog-category-translations.force_destroy', context: ['translation_uuid' => $uuid], message: $e->getMessage(), activity: __("{$this->langPath}.force_destroy.error", ['uuid' => $uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.force_destroy.flash_error"));
        }
    }
}
