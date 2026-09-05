<?php

namespace App\Enums\Blogs\Posts;

use function __;

enum BlogPostStatus: string
{
    case Draft = 'draft'; // Brouillon
    case Published = 'published'; // Publié
    case Archived = 'archived'; // Archivé
    case Scheduled = 'scheduled'; // Programmé

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('Brouillon'),
            self::Published => __('Publié'),
            self::Archived => __('Archivé'),
            self::Scheduled => __('Programmé'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Draft => 'badge bg-soft-danger',
            self::Published => 'badge bg-soft-success',
            self::Archived => 'badge bg-soft-secondary',
            self::Scheduled => 'badge bg-soft-warning',
        };
    }
}
