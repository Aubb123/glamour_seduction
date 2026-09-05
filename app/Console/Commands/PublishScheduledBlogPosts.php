<?php

namespace App\Console\Commands;

use App\Enums\Blogs\Posts\BlogPostStatus;
use App\Models\BlogPost;
use Illuminate\Console\Command;

class PublishScheduledBlogPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:publish-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publie les articles programmés dont la date de publication est atteinte';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // Met à jour le statut des articles programmés dont la date de publication est atteinte
        $count = BlogPost::where('status', BlogPostStatus::Scheduled)->where('published_at', '<=', getDateTime())->update(['status' => BlogPostStatus::Published]);

        $this->info("{$count} article(s) programmé(s) publié(s).");

        return self::SUCCESS;
    }
}
