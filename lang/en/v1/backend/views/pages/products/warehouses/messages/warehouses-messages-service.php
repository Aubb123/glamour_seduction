<?php

return [
    'warehouses' => [
        'default' => [
            'storage_required' => 'A default warehouse must be of the "storage" type.',
            'active_required' => 'A default warehouse must be active.',
        ],
        'update' => [
            'default_release_forbidden' => 'Cannot remove the default warehouse role until another warehouse has been set as default.',
            'default_deactivate_forbidden' => 'Cannot deactivate the default warehouse until another warehouse takes its place.',
            'default_type_forbidden' => 'Cannot change the default warehouse type until another warehouse takes its place.',
            'stock_mode_switch_has_global_stock' => 'Cannot switch this warehouse to "location" mode: it already has global stocks. Please remove them first.',
            'stock_mode_switch_has_location_stock' => 'Cannot switch this warehouse to "warehouse" mode: it already has location stocks. Please remove them first.',
        ],
        'change_status' => [
            'default_deactivate_forbidden' => 'Cannot deactivate the default warehouse. Please set another default warehouse first.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active warehouse. Please deactivate it first.',
            'already_deleted' => 'This warehouse is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed warehouses can be restored.',
        ],
        'force_destroy' => [
            'active_forbidden' => 'Cannot permanently delete an active warehouse.',
            'not_trashed_forbidden' => 'Only trashed warehouses can be permanently deleted.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this warehouse translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',

        // ────────────────────────────────────────────
        // Translations
        // ────────────────────────────────────────────
        'translations' => [
            'destroy' => [
                'warehouse_not_found' => 'Cannot delete this translation: the warehouse it is linked to cannot be found.',
                'translation_not_found' => 'The translation to delete cannot be found.',
                'last_translation_forbidden' => 'Cannot delete the last translation of the warehouse.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],
            'restore' => [
                'not_trashed_forbidden' => 'Only trashed translations can be restored.',
            ],
            'force_destroy' => [
                'warehouse_not_found' => 'Cannot permanently delete this translation: the warehouse it is linked to cannot be found.',
                'translation_not_found' => 'The translation to permanently delete cannot be found.',
                'last_translation_forbidden' => 'Cannot permanently delete the last translation of the warehouse.',
                'not_trashed_forbidden' => 'Only trashed translations can be permanently deleted.',
            ],
        ],
    ],
];
