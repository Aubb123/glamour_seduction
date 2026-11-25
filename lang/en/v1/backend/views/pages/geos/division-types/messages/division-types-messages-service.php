<?php

return [

    'divisionTypes' => [

        'activate' => [
            'already_active' => 'This division type is already active.',
        ],

        'deactivate' => [
            'already_inactive' => 'This division type is already inactive.',
        ],

        'destroy' => [
            'active_forbidden' => 'Cannot delete an active division type. Deactivate it first.',
            'already_deleted' => 'This division type is already deleted.',
            'dependencies_forbidden' => 'Cannot delete this division type: it is used by divisions.',
        ],

        'restore' => [
            'not_trashed_forbidden' => 'Only deleted division types can be restored.',
        ],

        'force_destroy' => [
            'not_trashed_forbidden' => 'Only deleted division types can be permanently deleted.',
            'active_forbidden' => 'Cannot permanently delete an active division type.',
            'dependencies_forbidden' => 'Cannot permanently delete this division type: it is used by divisions.',
        ],

        'trashedTranslationConflict' => 'Cannot update this division type translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',

        'translations' => [

            'destroy' => [
                'division_type_not_found' => 'Cannot delete this translation: the division type it is linked to is not found.',
                'translation_not_found' => 'The translation to delete is not found.',
                'last_translation_forbidden' => 'Cannot delete this translation because it is the last remaining translation for this division type.',
                'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            ],

            'restore' => [
                'not_trashed_forbidden' => 'Only deleted translations can be restored.',
            ],

            'force_destroy' => [
                'division_type_not_found' => 'Cannot permanently delete this translation: the division type it is linked to is not found.',
                'translation_not_found' => 'The translation to permanently delete is not found.',
                'last_translation_forbidden' => 'Cannot permanently delete this translation because it is the last remaining translation for this division type.',
                'not_trashed_forbidden' => 'Only deleted translations can be permanently deleted.',
            ],
        ],

    ],

];
