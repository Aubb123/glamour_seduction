<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;

trait HasAuditModelBehaviors
{
    use HasDatetime;
    use HasFactory;
    use HasUuid;
}
