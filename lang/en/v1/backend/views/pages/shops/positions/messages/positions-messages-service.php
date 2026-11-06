<?php

return [
    'positions' => [
        'activate' => ['already_active' => 'This position is already active.'],
        'deactivate' => ['already_inactive' => 'This position is already inactive.'],
        'destroy' => ['active_forbidden' => 'Cannot delete an active position. Please deactivate it first.', 'already_deleted' => 'This position is already deleted and cannot be deleted again.'],
        'restore' => ['not_trashed_forbidden' => 'Only positions already deleted or in the trash can be restored.'],
        'force_destroy' => ['not_trashed_forbidden' => 'Only positions already deleted or in the trash can be permanently deleted.'],
        'trashedTranslationConflict' => 'Cannot update this position translation (language :lang_id): it is in the trash. Restore it from the trash before updating it.',
    ],
    'positionsTranslations' => [
        'destroy' => ['position_not_found' => 'Cannot delete this translation: the related position could not be found.', 'translation_not_found' => 'The translation to delete could not be found.', 'last_translation_forbidden' => 'Cannot delete the last translation of the position.', 'already_deleted' => 'This translation is already deleted and cannot be deleted again.'],
        'restore' => ['not_trashed_forbidden' => 'Only translations already deleted or in the trash can be restored.'],
        'force_destroy' => ['position_not_found' => 'Cannot permanently delete this translation: the related position could not be found.', 'translation_not_found' => 'The translation to permanently delete could not be found.', 'last_translation_forbidden' => 'Cannot permanently delete the last translation of the position.', 'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be permanently deleted.'],
    ],
];
