<?php

use App\Helpers\Web\Backend\BackendHelper;
use App\Models\Currency;
use App\Services\V1\PricingService;
use Illuminate\Database\Eloquent\Model;

if (! function_exists('getEnvFolder')) {
    function getEnvFolder()
    {
        return env('APP_ENV') == 'local' ? 'storage/' : 'public/storage/';
    }
}

if (! function_exists('getDateTime')) {
    function getDateTime()
    {
        // $date = now()->addMinutes(60)->format('Y-m-d H:i:s'); // Heure actuel == now (+1heures)
        // $date = now()->format('Y-m-d H:i:s'); // Heure actuel == now (sans ajout de temps)

        $date = now();

        return $date;
    }
}
// Fonction pour récupérer les langs actives sur la plateforme

// Function backend ///////////////////////////////////////////////////////////////////////////////////////////////////////////////
// /////////////////// Function backend ////////////////////////////////////////////////////////////////////////////////////////////
// //////////////////////////////////////// Function backend ///////////////////////////////////////////////////////////////////////
// //////////////////////////////////////////////////////////// Function backend ///////////////////////////////////////////////////
// //////////////////////////////////////////////////////////////////////////////// Function backend ///////////////////////////////
if (! function_exists('formatDate')) {
    function formatDate($date, string $format): string
    {
        return BackendHelper::formatDate($date, $format);
    }
}

// Function getActiveLangs()
if (! function_exists('getActiveLangs')) {
    function getActiveLangs()
    {
        return BackendHelper::getActiveLangs();
    }
}

// Function deleteOldCloudFiles()
if (! function_exists('deleteOldCloudFiles')) {
    function deleteOldCloudFiles($entity, $fileType)
    {
        return BackendHelper::deleteOldCloudFiles($entity, $fileType);
    }
}

// Function assertTranslationUniqueness()
if (! function_exists('assertTranslationUniqueness')) {
    function assertTranslationUniqueness($entity, array $translations, string $entityLabel, string $backendLangPath, string $nameField = 'name', ?string $slugField = 'slug')
    {
        return BackendHelper::assertTranslationUniqueness($entity, $translations, $entityLabel, $backendLangPath, $nameField, $slugField);
    }
}

// Function assertTranslationRestore()
if (! function_exists('assertTranslationRestore')) {
    function assertTranslationRestore($translation, string $entityLabel, string $backendLangPath, string $nameField = 'name', ?string $slugField = 'slug')
    {
        return BackendHelper::assertTranslationRestore($translation, $entityLabel, $backendLangPath, $nameField, $slugField);
    }
}

// Function destroy_function
if (! function_exists('destroy_function')) {
    function destroy_function($entity)
    {
        return BackendHelper::destroy_function($entity);
    }
}

// Function restore_function
if (! function_exists('restore_function')) {
    function restore_function($entity)
    {
        return BackendHelper::restore_function($entity);
    }
}

// Function force_destroy_function
if (! function_exists('force_destroy_function')) {
    function force_destroy_function($entity)
    {
        return BackendHelper::force_destroy_function($entity);
    }
}

// Function getTranslation
if (! function_exists('getTranslation')) {
    function getTranslation($entity, $langId): ?Model
    {
        return $entity->translations->firstWhere('lang_id', $langId);
    }
}

// Function getArchitectureCloud architecture cloud d'une plateforme cloud donnée
if (! function_exists('getArchitectureCloud')) {
    function getArchitectureCloud()
    {
        return BackendHelper::getArchitectureCloud();
    }
}

// Function getActiveCloudPlatforms
if (! function_exists('getActiveCloudPlatforms')) {
    function getActiveCloudPlatforms()
    {
        return BackendHelper::getActiveCloudPlatforms();
    }
}

// Function getSidebarMenu
if (! function_exists('getSidebarMenu')) {
    function getSidebarMenu(): array
    {
        return BackendHelper::getSidebarMenu();
    }
}

// Function getCreateLinks
if (! function_exists('getCreateLinks')) {
    function getCreateLinks(): array
    {
        return BackendHelper::getCreateLinks();
    }
}

// Function isRouteActive
if (! function_exists('isRouteActive')) {
    function isRouteActive(array $patterns): bool
    {
        return BackendHelper::isRouteActive($patterns);
    }
}

// Function isChildRouteActive
if (! function_exists('isChildRouteActive')) {
    function isChildRouteActive(array $children): bool
    {
        return BackendHelper::isChildRouteActive($children);
    }
}

// Function getSettingValue
if (! function_exists('getSettingValue')) {
    function getSettingValue(string $key, ?string $locale = null): mixed
    {
        return BackendHelper::getSettingValue($key, $locale);
    }
}

// ─────────────────────────────────────────────
// Module 10 — Prix multi-devise (pont vers PricingService)
// ─────────────────────────────────────────────

// Function getDisplayCurrency : devise d'affichage (users.currency_id → session → pays → défaut)
if (! function_exists('getDisplayCurrency')) {
    function getDisplayCurrency($user = null): Currency
    {
        return app(PricingService::class)->resolveDisplayCurrency($user);
    }
}

// Function convertPrice : montant × taux cible / taux source
if (! function_exists('convertPrice')) {
    function convertPrice(float $amount, ?Currency $source = null, ?Currency $target = null): float
    {
        return app(PricingService::class)->convertPrice($amount, $source, $target);
    }
}

// Function formatPrice : arrondi selon decimal_places, symbole, locale
if (! function_exists('formatPrice')) {
    function formatPrice(float $amount, ?Currency $currency = null): string
    {
        return app(PricingService::class)->formatPrice($amount, $currency);
    }
}
