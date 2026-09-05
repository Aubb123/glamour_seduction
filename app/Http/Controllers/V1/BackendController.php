<?php

namespace App\Http\Controllers\V1;

use App\Enums\Blogs\Posts\BlogPostStatus;
use App\Enums\Clouds\Platforms\CloudPlatformStatus;
use App\Enums\Faqs\FaqStatus;
use App\Enums\Galleries\GalleryStatus;
use App\Enums\Permissions\PermissionStatus;
use App\Enums\Roles\RoleStatus;
use App\Enums\Subscribes\SubscribeStatus;
use App\Enums\Users\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Audit;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogPostComment;
use App\Models\BlogTag;
use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Models\CloudPlatform;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Subscribe;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class BackendController extends Controller
{
    public function backend_index(): View
    {
        // ─── Statistiques globales ─────────────────────────────────
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('status', UserStatus::Active)->count(),
                'suspended' => User::where('status', UserStatus::Suspended)->count(),
                'banned' => User::where('status', UserStatus::Banned)->count(),
            ],
            'roles' => [
                'total' => Role::count(),
                'active' => Role::where('status', RoleStatus::Active)->count(),
            ],
            'permissions' => [
                'total' => Permission::count(),
                'active' => Permission::where('status', PermissionStatus::Active)->count(),
            ],
            'subscribes' => [
                'total' => Subscribe::count(),
                'active' => Subscribe::where('status', SubscribeStatus::Active)->count(),
            ],
            'blogPosts' => [
                'total' => BlogPost::count(),
                'published' => BlogPost::where('status', BlogPostStatus::Published)->count(),
                'drafts' => BlogPost::where('status', BlogPostStatus::Draft)->count(),
                'archived' => BlogPost::where('status', BlogPostStatus::Archived)->count(),
                'scheduled' => BlogPost::where('status', BlogPostStatus::Scheduled)->count(),
                'views' => BlogPost::sum('views'),
            ],
            'blogComments' => [
                'total' => BlogPostComment::count(),
                'approved' => BlogPostComment::where('is_visible', true)->count(),
                'pending' => BlogPostComment::where('is_visible', false)->count(),
            ],
            'blogTags' => BlogTag::count(),
            'blogCategories' => BlogCategory::count(),
            'contacts' => [
                'total' => Contact::count(),
                'unread' => Contact::where('is_read', false)->count(),
            ],
            'galleries' => [
                'total' => Gallery::count(),
                'active' => Gallery::where('status', GalleryStatus::Active)->count(),
            ],
            'faqs' => [
                'total' => Faq::count(),
                'active' => Faq::where('status', FaqStatus::Active)->count(),
            ],
            'cloudPlatforms' => [
                'total' => CloudPlatform::count(),
                'active' => CloudPlatform::where('status', CloudPlatformStatus::Active)->count(),
            ],
            'cloudFolders' => CloudFolder::count(),
            'cloudFiles' => CloudFile::count(),
            'audits' => Audit::count(),
            'activityLogs' => ActivityLog::count(),
        ];

        // ─── Derniers utilisateurs inscrits ───────────────────────
        $latestUsers = User::with(['country'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ─── Derniers messages de contact ─────────────────────────
        $latestContacts = Contact::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ─── Derniers articles publiés ────────────────────────────
        $latestPosts = BlogPost::withActiveTranslation()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ─── Derniers abonnés ─────────────────────────────────────
        $latestSubscribes = Subscribe::with(['user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('v1.backend.pages.index', compact(
            'stats',
            'latestUsers',
            'latestContacts',
            'latestPosts',
            'latestSubscribes'
        ));
    }

    public function backend_analytics(): View
    {
        // ─── Filtres temporels ─────────────────────────────────────
        $period = request('period', 'month'); // hour, day, week, month, year, custom
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        // Déterminer la plage de dates selon la période
        $now = getDateTime();
        $start = match ($period) {
            'hour' => $now->copy()->startOfHour(),
            'day' => $now->copy()->startOfDay(),
            'week' => $now->copy()->startOfWeek(),
            'month' => $now->copy()->startOfMonth(),
            'year' => $now->copy()->startOfYear(),
            'custom' => $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : $now->copy()->startOfMonth(),
            default => $now->copy()->startOfMonth(),
        };
        $end = match ($period) {
            'hour' => $now->copy()->endOfHour(),
            'day' => $now->copy()->endOfDay(),
            'week' => $now->copy()->endOfWeek(),
            'month' => $now->copy()->endOfMonth(),
            'year' => $now->copy()->endOfYear(),
            'custom' => $dateTo ? Carbon::parse($dateTo)->endOfDay() : $now->copy()->endOfDay(),
            default => $now->copy()->endOfMonth(),
        };

        // ─── Agrégation des données par période ────────────────────
        $format = match ($period) {
            'hour' => 'H:00',
            'day' => 'H:00',
            'week' => 'D',
            'month' => 'd M',
            'year' => 'M Y',
            'custom' => 'd M Y',
            default => 'd M',
        };

        // Format SQL de regroupement selon la période
        $groupFormat = match ($period) {
            'hour', 'day' => '%Y-%m-%d %H:00:00',
            'week', 'month', 'custom' => '%Y-%m-%d',
            'year' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        // Fonction pour construire la requête d'agrégation
        $buildQuery = function ($model) use ($start, $end, $groupFormat) {
            return $model::whereBetween('created_at', [$start, $end])
                ->selectRaw("DATE_FORMAT(created_at, '{$groupFormat}') as date, COUNT(*) as count")
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('count', 'date');
        };

        $users = $buildQuery(User::class);
        $subscribes = $buildQuery(Subscribe::class);
        $contacts = $buildQuery(Contact::class);
        $posts = $buildQuery(BlogPost::class);
        $comments = $buildQuery(BlogPostComment::class);
        $audits = $buildQuery(Audit::class);
        $activityLogs = $buildQuery(ActivityLog::class);

        // ─── Générer les labels et séries ──────────────────────────
        $labels = [];
        $cursor = $start->copy();
        $step = match ($period) {
            'hour', 'day' => 'hour',
            'week', 'month', 'custom' => 'day',
            'year' => 'month',
            default => 'day',
        };
        while ($cursor <= $end) {
            $labels[] = $cursor->format($format);
            $step === 'hour' ? $cursor->addHour() : ($step === 'month' ? $cursor->addMonth() : $cursor->addDay());
        }

        $buildSeries = function ($data) use ($labels, $format) {
            $series = array_fill(0, count($labels), 0);
            foreach ($data as $date => $count) {
                $key = Carbon::parse($date)->format($format);
                $index = array_search($key, $labels);
                if ($index !== false) {
                    $series[$index] = $count;
                }
            }

            return $series;
        };

        $chartData = [
            'labels' => $labels,
            'users' => $buildSeries($users),
            'subscribes' => $buildSeries($subscribes),
            'contacts' => $buildSeries($contacts),
            'posts' => $buildSeries($posts),
            'comments' => $buildSeries($comments),
            'audits' => $buildSeries($audits),
            'activityLogs' => $buildSeries($activityLogs),
        ];

        // ─── Totaux sur la période ─────────────────────────────────
        $totals = [
            'users' => array_sum($chartData['users']),
            'subscribes' => array_sum($chartData['subscribes']),
            'contacts' => array_sum($chartData['contacts']),
            'posts' => array_sum($chartData['posts']),
            'comments' => array_sum($chartData['comments']),
            'audits' => array_sum($chartData['audits']),
            'activityLogs' => array_sum($chartData['activityLogs']),
        ];

        return view('v1.backend.pages.analytics', compact(
            'period',
            'dateFrom',
            'dateTo',
            'chartData',
            'totals',
            'start',
            'end'
        ));
    }
}
