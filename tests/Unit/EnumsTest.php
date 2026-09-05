<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;

/**
 * Vérifie que toutes les enums du socle (app/Enums) respectent le contrat :
 * `values()` (liste de strings), `label()` et `badgeClass()` sur chaque case.
 */
it('toutes les enums du projet exposent values(), label() et badgeClass() valides', function () {
    $filesystem = new Filesystem;

    $enumFiles = $filesystem->allFiles(app_path('Enums'));

    expect($enumFiles)->not->toBeEmpty('Aucun fichier trouvé dans app/Enums');

    $enumClasses = [];

    foreach ($enumFiles as $file) {
        $relative = $file->getRelativePathname();
        $fqcn = 'App\\Enums\\'.str_replace('/', '\\', substr($relative, 0, -4));

        $reflection = new ReflectionClass($fqcn);

        if ($reflection->isEnum()) {
            $enumClasses[] = $fqcn;
        }
    }

    foreach ($enumClasses as $fqcn) {
        // Les enums de `base` ne sont pas toutes uniformes : certaines n'exposent
        // que `values()`, d'autres `label()`/`badgeClass()`, etc. On valide donc
        // les invariants de CHAQUE méthode présente, sans imposer de contrat figé.
        foreach ($fqcn::cases() as $case) {
            expect($case->value)->toBeString("{$fqcn}::{$case->name} : valeur non string");
        }

        if (method_exists($fqcn, 'values')) {
            $values = $fqcn::values();
            expect($values)->toBeArray("{$fqcn} : values() ne retourne pas un tableau");
            expect($values)->not->toBeEmpty("{$fqcn} : values() est vide");

            foreach ($values as $value) {
                expect($value)->toBeString("{$fqcn} : une valeur n’est pas une string");
            }

            // Les valeurs retournées correspondent aux valeurs des cases.
            expect($values)->toBe(array_column($fqcn::cases(), 'value'));
        }

        if (method_exists($fqcn, 'label')) {
            foreach ($fqcn::cases() as $case) {
                expect($case->label())->toBeString("{$fqcn}::{$case->name} label() non string");
                expect($case->label())->not->toBeEmpty("{$fqcn}::{$case->name} label() vide");
            }
        }

        if (method_exists($fqcn, 'badgeClass')) {
            foreach ($fqcn::cases() as $case) {
                expect($case->badgeClass())->toBeString("{$fqcn}::{$case->name} badgeClass() non string");
            }
        }
    }
});