<?php

return [
    'shops' => [
        'activate' => [
            'already_active' => 'This shop is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This shop is already inactive.',
        ],
        'update' => [
            'default_forbidden' => 'Cannot remove the "default" status from the last default shop. Set another default shop first.',
        ],
        'warehouse_required' => 'A shop must be linked to at least one warehouse.',
        'warehouse_not_found' => 'One or more selected warehouses could not be found.',
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active shop. Please deactivate it first.',
            'already_deleted' => 'This shop is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only shops already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only shops already deleted or in the trash can be permanently deleted.',
        ],
        'trashedTranslationConflict' => 'Cannot update this shop translation (language :lang_id): it is in the trash. Restore it from the trash before updating it.',
    ],
    'shopsTranslations' => [
        'destroy' => [
            'shop_not_found' => 'Cannot delete this translation: the related shop could not be found.',
            'translation_not_found' => 'The translation to delete could not be found.',
            'last_translation_forbidden' => 'Cannot delete the last translation of the shop.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'shop_not_found' => 'Cannot permanently delete this translation: the related shop could not be found.',
            'translation_not_found' => 'The translation to permanently delete could not be found.',
            'last_translation_forbidden' => 'Cannot permanently delete the last translation of the shop.',
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be permanently deleted.',
        ],
    ],
];
