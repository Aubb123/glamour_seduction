<?php

declare(strict_types=1);

use App\Helpers\Web\Backend\BackendHelper;
use App\Helpers\Web\Frontend\FrontendHelper;
use App\Services\V1\ActivityLogger;
use App\Services\V1\VersionService;

it('les 10 traits du socle sont présents', function () {
    $traits = [
        'HasActivityLogs', 'HasAudit', 'HasAuditModelBehaviors', 'HasCloudFiles',
        'HasDatetime', 'HasModelBehaviors', 'HasSlug', 'HasTranslations',
        'HasUuid', 'LogActivity',
    ];

    foreach ($traits as $trait) {
        expect(trait_exists("App\\Traits\\{$trait}"))->toBeTrue("Trait manquant : {$trait}");
    }
});

it('les services transverses et classes helpers sont présents', function () {
    expect(class_exists(BackendHelper::class))->toBeTrue();
    expect(class_exists(FrontendHelper::class))->toBeTrue();

    expect(class_exists(VersionService::class))->toBeTrue();
    expect(class_exists(ActivityLogger::class))->toBeTrue();
});
