<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasModelBehaviors
{
    use HasActivityLogs;
    use HasAudit;
    use HasCloudFiles;
    use HasDatetime;
    use HasFactory;
    use HasSlug;
    use HasTranslations;
    use HasUuid;
    use SoftDeletes;
}
