<?php

return [
    'productWarehouses' => [
        'activate' => [
            'already_active' => 'This warehouse is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This warehouse is already inactive.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active warehouse. Please deactivate it first.',
            'already_deleted' => 'This warehouse is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only warehouses already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only warehouses already deleted or in the trash can be permanently deleted.',
        ],
        // Refus de modifier silencieusement une traduction placée en corbeille
        'trashedTranslationConflict' => 'A translation already exists in the trash for this language (lang_id: :lang_id). Restore it or permanently delete it before saving.',
    ],
    'productWarehousesTranslations' => [
        'destroy' => [
            'warehouse_not_found' => 'The parent warehouse of this translation could not be found.',
            'translation_not_found' => 'The translation could not be found.',
            'already_deleted' => 'This translation is already deleted and cannot be deleted again.',
            'last_translation_forbidden' => 'Cannot delete the last translation of the warehouse.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'warehouse_not_found' => 'The parent warehouse of this translation could not be found.',
            'translation_not_found' => 'The translation could not be found.',
            'not_trashed_forbidden' => 'Only translations already deleted or in the trash can be permanently deleted.',
            'last_translation_forbidden' => 'Cannot permanently delete the last translation of the warehouse.',
        ],
    ],
    'warehouseUsers' => [
        'store' => [
            'success' => 'Assigning a staff member to the warehouse: :name',
            'error' => 'Error while assigning the staff member',
            'not_found' => 'User or position not found.',
            'flash_success' => 'Staff member (:name) successfully assigned.',
            'flash_error' => 'An error occurred.',
        ],
        'update' => [
            'success' => 'Updating the assignment: :name',
            'warning_meta' => 'Business error while updating the assignment: :uuid',
            'error' => 'Error while updating the assignment: :uuid',
            'flash_success' => 'Assignment (:name) successfully updated.',
            'flash_error' => 'An error occurred.',
        ],
        'destroy' => [
            'success' => 'Removing a staff member: :name',
            'error' => 'Error while removing the staff member: :uuid',
            'flash_success' => 'Staff member (:name) successfully removed.',
            'flash_error' => 'An error occurred.',
        ],
    ],
];
