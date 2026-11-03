<?php

return [
    'productAttributes' => [
        'activate' => [
            'already_active' => 'This attribute is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This attribute is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active attribute. Please deactivate it first.',
            'already_deleted' => 'This attribute is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only attributes already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only attributes already deleted or in the trash can be permanently deleted.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this attribute translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',
    ],

    'productAttributesTranslations' => [
        'destroy' => [
            'attribute_not_found' => 'Cannot delete this translation: the attribute it is linked to cannot be found.',
            'translation_not_found' => 'The translation to delete cannot be found.',
            'last_translation_forbidden' => 'Cannot delete the last translation of the attribute.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'attribute_not_found' => 'Cannot permanently delete this translation: the attribute it is linked to cannot be found.',
            'translation_not_found' => 'The translation to permanently delete cannot be found.',
            'last_translation_forbidden' => 'Cannot permanently delete the last translation of the attribute.',
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be permanently deleted.',
        ],
    ],
];
