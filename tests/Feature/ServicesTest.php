<?php

declare(strict_types=1);

use App\Services\V1\VersionService;
use Illuminate\Support\Facades\Blade;

it('VersionService::getActive() retourne null tant que la table platform_versions n’existe pas', function () {
    expect(app(VersionService::class)->getActive())->toBeNull();
});

it('la directive Blade @formatDate est enregistrée par AppServiceProvider', function () {
    $directives = Blade::getCustomDirectives();

    expect($directives)->toHaveKey('formatDate');
});