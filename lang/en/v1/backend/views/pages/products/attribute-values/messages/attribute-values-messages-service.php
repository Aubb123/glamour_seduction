<?php

return [
    'productAttributeValues' => [
        'activate' => [
            'already_active' => 'This attribute value is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This attribute value is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active attribute value. Please deactivate it first.',
            'already_deleted' => 'This attribute value is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only attribute values already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only attribute values already deleted or in the trash can be permanently deleted.',
        ],

        // Refusal to update a translation sitting in the trash (syncTranslations guard)
        'trashedTranslationConflict' => 'Cannot update this attribute value translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',
    ],

    'productAttributeValuesTranslations' => [
        'destroy' => [
            'attribute_value_not_found' => 'Cannot delete this translation: the attribute value it is linked to cannot be found.',
            'translation_not_found' => 'The translation to delete cannot be found.',
            'last_translation_forbidden' => 'Cannot delete the last translation of the attribute value.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'attribute_value_not_found' => 'Cannot permanently delete this translation: the attribute value it is linked to cannot be found.',
            'translation_not_found' => 'The translation to permanently delete cannot be found.',
            'last_translation_forbidden' => 'Cannot permanently delete the last translation of the attribute value.',
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be permanently deleted.',
        ],
    ],
];
