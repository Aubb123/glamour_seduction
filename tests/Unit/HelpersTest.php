<?php

declare(strict_types=1);

use Carbon\Carbon;

it('tous les helpers applicatifs sont déclarés', function () {
    $helpers = [
        'getEnvFolder', 'getDateTime', 'formatDate', 'getActiveLangs',
        'deleteOldCloudFiles', 'assertTranslationUniqueness',
        'assertTranslationRestore', 'destroy_function', 'restore_function',
        'force_destroy_function', 'getTranslation', 'getArchitectureCloud',
        'getActiveCloudPlatforms', 'getSidebarMenu', 'getCreateLinks',
        'isRouteActive', 'isChildRouteActive', 'getSettingValue',
    ];

    foreach ($helpers as $helper) {
        expect(function_exists($helper))->toBeTrue("Fonction helper manquante : {$helper}");
    }
});

it('getEnvFolder retourne un dossier d’assets terminant par un slash', function () {
    expect(getEnvFolder())->toBeString();
    expect(getEnvFolder())->toEndWith('/');
});

it('getDateTime retourne une instance de Carbon', function () {
    expect(getDateTime())->toBeInstanceOf(Carbon::class);
});

it('formatDate formate une date et renvoie un tiret pour les valeurs vides', function () {
    expect(formatDate('2024-01-15', 'DD MMMM YYYY'))->toBeString();
    expect(formatDate('2024-01-15', 'DD MMMM YYYY'))->toContain('Jan');
    expect(formatDate(null, 'DD MMMM YYYY'))->toBe('—');
});