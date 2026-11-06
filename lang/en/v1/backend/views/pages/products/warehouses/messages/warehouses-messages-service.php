<?php

return [
    'productWarehouses' => [
        'activate' => [
            'already_active' => 'This warehouse is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This warehouse is already inactive.',
            'default_forbidden' => 'Cannot deactivate the default warehouse. Set another default warehouse first.',
        ],
        'update' => [
            'default_forbidden' => 'Cannot remove the "default" status from the last default warehouse. Set another default warehouse first.',
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
