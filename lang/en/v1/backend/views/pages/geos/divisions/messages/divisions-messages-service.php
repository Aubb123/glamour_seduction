<?php

return [

    'divisions' => [

        'activate' => [
            'already_active' => 'This division is already active.',
        ],

        'deactivate' => [
            'already_inactive' => 'This division is already inactive.',
        ],

        'destroy' => [
            'active_forbidden' => 'Unable to delete an active division. Please deactivate it first.',
            'already_deleted' => 'This division has already been deleted.',
            'has_children' => 'Unable to delete this division: it has sub-divisions. Please delete its sub-divisions first.',
        ],

        'restore' => [
            'not_trashed_forbidden' => 'Only deleted divisions can be restored.',
            'parent_trashed' => 'Unable to restore this division: its parent division is in the trash. Please restore the parent first.',
        ],

        'force_destroy' => [
            'not_trashed_forbidden' => 'Only deleted divisions can be permanently deleted.',
            'active_forbidden' => 'Unable to permanently delete an active division.',
            'has_children' => 'Unable to permanently delete this division: it has sub-divisions (even in the trash).',
        ],

        'trashedTranslationConflict' => 'Unable to update this division translation (language :lang_id): it is in the trash. Please restore it from the trash before updating it.',

        'translations' => [

            'destroy' => [
                'division_not_found' => 'Unable to delete this translation: the division it is linked to could not be found.',
                'translation_not_found' => 'The translation to delete could not be found.',
                'last_translation_forbidden' => 'Unable to delete this translation because it is the last remaining translation for this division.',
                'already_deleted' => 'This translation has already been deleted and cannot be deleted again.',
            ],

            'restore' => [
                'not_trashed_forbidden' => 'Only deleted translations can be restored.',
                'slug_conflict' => 'Unable to restore this translation: the slug is already used by another translation in the same language.',
            ],

            'force_destroy' => [
                'division_not_found' => 'Unable to permanently delete this translation: the division it is linked to could not be found.',
                'translation_not_found' => 'The translation to permanently delete could not be found.',
                'last_translation_forbidden' => 'Unable to permanently delete this translation because it is the last remaining translation for this division.',
                'not_trashed_forbidden' => 'Only deleted translations can be permanently deleted.',
            ],
        ],

    ],

];
