<?php

return [

    'currencies' => [

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
