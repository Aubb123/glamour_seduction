<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\User\UpdateUserRequest;
use App\Models\GeoCountry;
use App\Models\Role;
use App\Models\User;
use App\Services\V1\Controllers\UserService;
use App\Traits\LogActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    use LogActivity; // Trait pour le logging d'activité

    // Chemin de base pour les traductions (centralisé)
    protected string $langPath = 'v1/backend/views/pages/users/messages/user-messages-controller.users';

    // Injection de dépendance du service de gestion des utilisateurs
    public function __construct(protected UserService $userService) {}

    // ═══════════════════════════════════════════════════════════════
    // BACKEND
    // ═══════════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────
    public function backend_users_index(): View
    {
        try {

            // ✅ Récupère tous les utilisateurs avec leur pays et leurs rôles, triés par date de création décroissante
            $users = User::with(['cloudFiles'])->withCount(['activityLogs', 'galleries', 'subscribes', 'audits', 'blogPosts', 'blogPostComments', 'blogPostCommentReplies'])->orderBy('created_at', 'desc')->get();

            // Log de l'accès à la liste des utilisateurs
            $this->logActivity(type: 'info', action: 'backend.users.index', context: ['count' => $users->count()], message: null, activity: __("{$this->langPath}.index.success"), is_success: true);

            // Affiche la vue avec les utilisateurs récupérés
            return view('v1.backend.pages.users.index', compact('users'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur avec le message d'exception
            $this->logActivity(type: 'error', action: 'backend.users.index', context: [], message: $e->getMessage(), activity: __("{$this->langPath}.index.error"), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération des utilisateurs
        }
    }

    // ─────────────────────────────────────────────
    // Résolution + vérification slug (helper privé)
    // ─────────────────────────────────────────────
    private function resolveUserWithSlugCheck(User $user, string $slug, string $action, string $langKey): User
    {
        $expectedSlug = $user->slug ?? 'null';

        if ($expectedSlug !== $slug) {
            // Log de l'erreur de correspondance du slug avec un message d'avertissement
            $this->logActivity(type: 'warning', action: $action, context: ['user_uuid' => $user->uuid, 'user_slug' => $slug, 'expected_slug' => $expectedSlug], message: null, activity: __("{$this->langPath}.{$langKey}.slug_mismatch"), is_success: false, loggable: $user);

            abort(404);
        }

        return $user;
    }

    // ─────────────────────────────────────────────
    // SHOW
    // ─────────────────────────────────────────────
    public function backend_users_show(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.show', 'show');

            // Charge les compteurs (withCount)
            $user->loadCount(['activityLogs', 'galleries', 'subscribes', 'audits', 'blogPosts', 'blogPostComments', 'blogPostCommentReplies']);

            $userName = $user->first_name.' '.$user->last_name;

            // Log de la consultation du détail d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.show', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.show.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue de détail de l'utilisateur
            return view('v1.backend.pages.users.show', compact('user'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du détail d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.show', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.show.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de l'utilisateur
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVITY LOGS
    // ─────────────────────────────────────────────
    public function backend_users_activity_logs(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.activity-logs', 'activity-logs');

            $userName = $user->first_name.' '.$user->last_name;

            $logs = $user->activityLogs()->orderBy('created_at', 'desc')->get();

            // Log de la consultation du journal d'activité d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.activity-logs', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.activity-logs.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue du journal d'activité de l'utilisateur
            return view('v1.backend.pages.users.others.activity-logs', compact('user', 'logs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du journal d'activité d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.activity-logs', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activity-logs.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de l'utilisateur
        }
    }

    // ─────────────────────────────────────────────
    // SUBSCRIBES
    // ─────────────────────────────────────────────
    public function backend_users_subscribes(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.subscribes', 'subscribes');

            $userName = $user->first_name.' '.$user->last_name;

            $subscribes = $user->subscribes()->orderBy('created_at', 'desc')->get();

            // Log de la consultation des abonnements d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.subscribes', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.subscribes.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue des abonnements de l'utilisateur
            return view('v1.backend.pages.users.others.subscribes', compact('user', 'subscribes'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des abonnements d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.subscribes', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.subscribes.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // GALLERIES
    // ─────────────────────────────────────────────
    public function backend_users_galleries(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.galleries', 'galleries');

            $userName = $user->first_name.' '.$user->last_name;

            $galleries = $user->galleries()->withActiveTranslation()->with(['cloudFiles'])->orderBy('sort_order', 'desc')->get();

            // Log de la consultation des galeries d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.galleries', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.galleries.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue des galeries de l'utilisateur
            return view('v1.backend.pages.users.others.galleries', compact('user', 'galleries'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des galeries d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.galleries', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.galleries.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de l'utilisateur
        }
    }

    // ─────────────────────────────────────────────
    // CONTACTS
    // ─────────────────────────────────────────────
    public function backend_users_contacts(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.contacts', 'contacts');

            $userName = $user->first_name.' '.$user->last_name;

            $contacts = $user->contacts()->orderBy('created_at', 'desc')->get();

            // Log de la consultation des messages de contact d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.contacts', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.contacts.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue des messages de contact de l'utilisateur
            return view('v1.backend.pages.users.others.contacts', compact('user', 'contacts'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des messages de contact d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.contacts', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.contacts.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // FAQS
    // ─────────────────────────────────────────────
    public function backend_users_faqs(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.faqs', 'faqs');

            $userName = $user->first_name.' '.$user->last_name;

            $faqs = $user->faqs()->with(['user'])->orderBy('sort_order', 'desc')->get();

            // Log de la consultation des FAQ de l'utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.faqs', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.faqs.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue des FAQ de l'utilisateur
            return view('v1.backend.pages.users.others.faqs', compact('user', 'faqs'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des FAQ d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.faqs', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.faqs.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500);
        }
    }

    // ─────────────────────────────────────────────
    // AUDITS
    // ─────────────────────────────────────────────
    public function backend_users_audits(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.audits', 'audits');

            $userName = $user->first_name.' '.$user->last_name;

            $audits = $user->audits()->with(['user', 'auditable'])->orderBy('created_at', 'desc')->get();

            // Log de la consultation du journal d'audit d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.audits', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.audits.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue du journal d'audit de l'utilisateur
            return view('v1.backend.pages.users.others.audits', compact('user', 'audits'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation du journal d'audit d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.audits', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.audits.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de l'utilisateur
        }
    }

    // ─────────────────────────────────────────────
    // BLOG POSTS
    // ─────────────────────────────────────────────
    public function backend_users_blog_posts(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.blog-posts', 'blog-posts');

            $userName = $user->first_name.' '.$user->last_name;

            $posts = $user->blogPosts()->withActiveTranslation()->with(['cloudFiles'])->orderBy('sort_order', 'desc')->get();

            // Log de la consultation des articles de blog d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.blog-posts', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.blog-posts.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue des articles de blog de l'utilisateur
            return view('v1.backend.pages.users.others.blog-posts', compact('user', 'posts'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des articles de blog d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.blog-posts', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.blog-posts.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de l'utilisateur
        }
    }

    // ─────────────────────────────────────────────
    // BLOG POST COMMENTS
    // ─────────────────────────────────────────────
    public function backend_users_blog_post_comments(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.blog-post-comments', 'blog-post-comments');

            $userName = $user->first_name.' '.$user->last_name;

            $comments = $user->blogPostComments()->with(['post', 'user'])->orderBy('created_at', 'desc')->get();

            // Log de la consultation des commentaires de blog d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.blog-post-comments', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.blog-post-comments.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue des commentaires de blog de l'utilisateur
            return view('v1.backend.pages.users.others.blog-post-comments', compact('user', 'comments'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des commentaires de blog d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.blog-post-comments', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.blog-post-comments.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de l'utilisateur
        }
    }

    // ─────────────────────────────────────────────
    // BLOG POST COMMENT REPLIES
    // ─────────────────────────────────────────────
    public function backend_users_blog_post_comment_replies(User $user, string $slug): View
    {
        try {

            // Vérifie que le slug correspond
            $user = $this->resolveUserWithSlugCheck($user, $slug, 'backend.users.blog-post-comment-replies', 'blog-post-comment-replies');

            $userName = $user->first_name.' '.$user->last_name;

            $replies = $user->blogPostCommentReplies()->with(['comment', 'user'])->orderBy('created_at', 'desc')->get();

            // Log de la consultation des réponses de commentaires de blog d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.blog-post-comment-replies', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.blog-post-comment-replies.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Retourne la vue des réponses de commentaires de blog de l'utilisateur
            return view('v1.backend.pages.users.others.blog-post-comment-replies', compact('user', 'replies'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la consultation des réponses de commentaires de blog d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.blog-post-comment-replies', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.blog-post-comment-replies.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de l'utilisateur
        }
    }

    // ─────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────
    public function backend_users_edit(User $user): View
    {
        try {

            // Récupère la liste des rôles et des pays pour les sélecteurs du formulaire
            $roles = Role::orderBy('name', 'asc')->get();
            $countries = GeoCountry::withActiveTranslation()->orderBy('sort_order', 'asc')->get();

            // Log de l'accès à la page de modification d'un utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.edit', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.edit.success", ['name' => $user->first_name.' '.$user->last_name]), is_success: true, loggable: $user);

            // Retourne la vue d'édition de l'utilisateur avec les données nécessaires
            return view('v1.backend.pages.users.edit', compact('user', 'roles', 'countries'));

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de l'accès à la page de modification d'un utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.edit', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.edit.error", ['uuid' => $user->uuid]), is_success: false);

            abort(500); // Erreur serveur générique en cas de problème lors de la récupération de l'utilisateur
        }
    }

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_users_update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {

            // Mise à jour de l'utilisateur via le service, en passant les données validées du formulaire
            $this->userService->backend_users_update($user, $request->validated());

            // Rafraîchit l'instance pour capturer les vrais changements
            $user->refresh();
            $userName = $user->first_name.' '.$user->last_name;

            // Log de la mise à jour de l'utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.update', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email, 'changes' => $user->getChanges()], message: null, activity: __("{$this->langPath}.update.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Redirection vers la page de détail de l'utilisateur avec un message de succès
            return redirect()->route('backend.users.show', ['user' => $user->uuid, 'slug' => $user->slug])->with('success', __("{$this->langPath}.update.flash_success", ['name' => $userName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la mise à jour de l'utilisateur
            $this->logActivity(type: 'warning', action: 'backend.users.update', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.warning_meta", ['uuid' => $user->uuid]), is_success: false);

            // Redirection avec le message métier explicite
            return redirect()->back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur lors de la mise à jour de l'utilisateur
            $this->logActivity(type: 'error', action: 'backend.users.update', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.update.error", ['uuid' => $user->uuid]), is_success: false);

            // Redirection avec un message d'erreur
            return back()->with('error', __("{$this->langPath}.update.flash_error"))->withInput();
        }
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_users_activate(Request $request, User $user): RedirectResponse
    {
        try {

            // Activation de l'utilisateur via le service
            $this->userService->backend_users_activate($user);

            // Récupération du nom complet de l'utilisateur pour le message de succès
            $userName = $user->first_name.' '.$user->last_name;

            // Log de l'activation de l'utilisateur
            $this->logActivity(type: 'info', action: 'backend.users.activate', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.activate.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.activate.flash_success", ['name' => $userName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de l'activation
            $this->logActivity(type: 'warning', action: 'backend.users.activate', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.warning_meta", ['uuid' => $user->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.users.activate', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.activate.error", ['uuid' => $user->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.activate.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // SUSPEND
    // ─────────────────────────────────────────────
    public function backend_users_suspend(Request $request, User $user): RedirectResponse
    {
        try {

            // Suspension de l'utilisateur via le service
            $this->userService->backend_users_suspend($user);
            $userName = $user->first_name.' '.$user->last_name;

            // Log de la suspension de l'utilisateur
            $this->logActivity(type: 'warning', action: 'backend.users.suspend', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.suspend.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.suspend.flash_success", ['name' => $userName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors de la suspension
            $this->logActivity(type: 'warning', action: 'backend.users.suspend', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.suspend.warning_meta", ['uuid' => $user->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.users.suspend', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.suspend.error", ['uuid' => $user->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.suspend.flash_error"));
        }
    }

    // ─────────────────────────────────────────────
    // BAN
    // ─────────────────────────────────────────────
    public function backend_users_ban(Request $request, User $user): RedirectResponse
    {
        try {

            // Bannissement de l'utilisateur via le service
            $this->userService->backend_users_ban($user);
            $userName = $user->first_name.' '.$user->last_name;

            // Log du bannissement de l'utilisateur
            $this->logActivity(type: 'warning', action: 'backend.users.ban', context: ['user_uuid' => $user->uuid, 'user_email' => $user->email], message: null, activity: __("{$this->langPath}.ban.success", ['name' => $userName]), is_success: true, loggable: $user);

            // Redirection avec un message de succès
            return back()->with('success', __("{$this->langPath}.ban.flash_success", ['name' => $userName]));

        } catch (\InvalidArgumentException $e) {

            // Log de l'erreur métier lors du bannissement
            $this->logActivity(type: 'warning', action: 'backend.users.ban', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.ban.warning_meta", ['uuid' => $user->uuid]), is_success: false);

            return back()->with('error', $e->getMessage())->withInput();

        } catch (\Exception $e) {

            // Log de l'erreur serveur
            $this->logActivity(type: 'error', action: 'backend.users.ban', context: ['user_uuid' => $user->uuid], message: $e->getMessage(), activity: __("{$this->langPath}.ban.error", ['uuid' => $user->uuid]), is_success: false);

            return back()->with('error', __("{$this->langPath}.ban.flash_error"));
        }
    }
}
