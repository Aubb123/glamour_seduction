<?php

return [

    'currencies' => [

        // ─────────────────────────────────────────────
        // UPDATE (Module 10 : exchange rate)
        // ─────────────────────────────────────────────
        'update' => [
            'exchange_rate_default_forbidden' => 'The default currency is the pricing base: its exchange rate must stay fixed at 1.000000.',
        ],

        // ─────────────────────────────────────────────
        // PRICING (Module 10 : multi-currency pricing)
        // ─────────────────────────────────────────────
        'pricing' => [
            'invalid_exchange_rate' => 'Conversion impossible: the exchange rate of currency “:code” is invalid or missing.',
        ],
        // ─────────────────────────────────────────────
        // ACTIVATE
        // ─────────────────────────────────────────────
        'activate' => [
            'already_active' => 'This currency is already active.',
        ],

        // ─────────────────────────────────────────────
        // DEACTIVATE
        // ─────────────────────────────────────────────
        'deactivate' => [
            'already_inactive' => 'This currency is already inactive.',
        ],

        // ─────────────────────────────────────────────
        // DESTROY
        // ─────────────────────────────────────────────
        'destroy' => [
            'default_currency_forbidden' => 'This currency cannot be deleted because it is set as the platform default currency. Set another currency as default first.',
            'already_deleted' => 'This currency is already deleted and cannot be deleted again.',
        ],

        // ─────────────────────────────────────────────
        // RESTORE
        // ─────────────────────────────────────────────
        'restore' => [
            'not_trashed_forbidden' => 'Only deleted currencies can be restored.',
        ],

        // ─────────────────────────────────────────────
        // FORCE DESTROY
        // ─────────────────────────────────────────────
        'force_destroy' => [
            'default_currency_forbidden' => 'This currency cannot be permanently deleted because it is set as the platform default currency. Set another currency as default first.',
            'not_trashed_forbidden' => 'Only deleted currencies can be permanently deleted.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this currency translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',

        // ─────────────────────────────────────────────
        // TRANSLATIONS
        // ─────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'last_translation_forbidden' => 'Cannot delete this translation because it is the last remaining translation for this currency.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'last_translation_forbidden' => 'Cannot permanently delete this translation because it is the last remaining translation for this currency.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],

    ],

];
