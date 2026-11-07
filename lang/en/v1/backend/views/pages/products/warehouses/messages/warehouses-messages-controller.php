<?php

return [
    'productWarehouses' => [
        'not_found' => 'Attempt to access a missing warehouse (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying the warehouses list',
            'error' => 'Error while displaying the warehouses list',
        ],
        'create' => [
            'success' => 'Access to the warehouse creation page',
            'error' => 'Error while accessing the warehouse creation page',
        ],
        'store' => [
            'success' => 'Creating a new warehouse: :name',
            'error' => 'Error while creating the warehouse',
            'flash_success' => 'Warehouse (:name) successfully created.',
            'flash_error' => 'An error occurred.',
        ],
        'show' => [
            'success' => 'Viewing warehouse details: :name',
            'warning' => 'Attempt to view a missing warehouse: :uuid',
            'error' => 'Error while viewing the warehouse: :uuid',
        ],
        'audits' => [
            'success' => 'Viewing warehouse audits: :name',
            'warning' => 'Attempt to view audits of a missing warehouse: :uuid',
            'error' => 'Error while viewing the warehouse audits: :uuid',
        ],
        'activity-logs' => [
            'success' => 'Viewing warehouse activity logs: :name',
            'warning' => 'Attempt to view activity logs of a missing warehouse: :uuid',
            'error' => 'Error while viewing the warehouse activity logs: :uuid',
        ],
        'edit' => [
            'success' => 'Access to the warehouse edit page: :name',
            'warning' => 'Attempt to edit a missing warehouse: :uuid',
            'error' => 'Error while accessing the warehouse edit page: :uuid',
        ],
        'update' => [
            'success' => 'Updating the warehouse in database: :name',
            'warning' => 'Attempt to update a missing warehouse: :uuid',
            'warning_meta' => 'Business error while updating the warehouse: :uuid',
            'error' => 'Error while updating the warehouse: :uuid',
            'flash_success' => 'Warehouse (:name) successfully updated.',
            'flash_warning' => 'Warehouse not found.',
            'flash_error' => 'An error occurred.',
        ],
        'activate' => [
            'success' => 'Activating the warehouse: :name',
            'error' => 'Error while activating the warehouse: :uuid',
            'flash_success' => 'Warehouse (:name) successfully activated.',
            'flash_error' => 'An error occurred.',
            'warning' => 'Attempt to activate a missing warehouse: :uuid',
            'flash_warning' => 'Warehouse not found.',
            'warning_meta' => 'Business error while activating the warehouse: :uuid',
        ],
        'deactivate' => [
            'success' => 'Deactivating the warehouse: :name',
            'error' => 'Error while deactivating the warehouse: :uuid',
            'flash_success' => 'Warehouse (:name) successfully deactivated.',
            'flash_error' => 'An error occurred.',
            'warning' => 'Attempt to deactivate a missing warehouse: :uuid',
            'flash_warning' => 'Warehouse not found.',
            'warning_meta' => 'Business error while deactivating the warehouse: :uuid',
        ],
        'destroy' => [
            'success' => 'Deleting the warehouse: :name',
            'warning' => 'Attempt to delete a missing warehouse: :uuid',
            'warning_meta' => 'Business error while deleting the warehouse: :uuid',
            'error' => 'Error while deleting the warehouse: :uuid',
            'flash_success' => 'Warehouse (:name) successfully deleted.',
            'flash_warning' => 'Warehouse not found.',
            'flash_error' => 'An error occurred.',
            'unknown' => 'Unknown warehouse',
        ],
        'trashed' => [
            'success' => 'Displaying the deleted warehouses list',
            'error' => 'Error while displaying the deleted warehouses list',
        ],
        'restore' => [
            'success' => 'Restoring the warehouse: :name',
            'warning' => 'Attempt to restore a missing warehouse: :uuid',
            'warning_meta' => 'Business error while restoring the warehouse: :uuid',
            'error' => 'Error while restoring the warehouse: :uuid',
            'flash_success' => 'Warehouse (:name) successfully restored.',
            'flash_warning' => 'Warehouse not found.',
            'flash_error' => 'An error occurred.',
        ],
        'force_destroy' => [
            'success' => 'Permanently deleting the warehouse: :name',
            'warning' => 'Attempt to permanently delete a missing warehouse: :uuid',
            'warning_meta' => 'Business error while permanently deleting the warehouse: :uuid',
            'error' => 'Error while permanently deleting the warehouse: :uuid',
            'flash_success' => 'Warehouse successfully deleted permanently.',
            'flash_warning' => 'Warehouse not found.',
            'flash_error' => 'An error occurred.',
        ],

        // Traductions (product_warehouse_translations)
        'translations' => [
            'not_found' => 'Attempt to access a missing warehouse translation (UUID: :uuid).',
            'destroy' => [
                'success' => 'Deleting translation: :uuid',
                'warning' => 'Attempt to delete a missing translation: :uuid',
                'warning_meta' => 'Validation error while deleting translation: :uuid',
                'error' => 'Error while deleting translation: :uuid',
                'flash_success' => 'Translation deleted successfully.',
                'flash_warning' => 'Translation not found.',
                'flash_error' => 'An error occurred.',
            ],
            'trashed' => [
                'accessed' => 'Displaying deleted translations for warehouse: :name',
                'not_found' => 'Warehouse not found for viewing deleted translations: :uuid',
                'error' => 'Error while displaying deleted translations: :uuid',
            ],
            'restore' => [
                'success' => 'Restoring translation: :uuid',
                'warning' => 'Attempt to restore a missing translation: :uuid',
                'warning_meta' => 'Validation error while restoring translation: :uuid',
                'error' => 'Error while restoring translation: :uuid',
                'flash_success' => 'Translation restored successfully.',
                'flash_warning' => 'Translation not found.',
                'flash_error' => 'An error occurred.',
            ],
            'force_destroy' => [
                'success' => 'Permanently deleting translation: :uuid',
                'warning' => 'Attempt to permanently delete a missing translation: :uuid',
                'warning_meta' => 'Validation error while permanently deleting translation: :uuid',
                'error' => 'Error while permanently deleting translation: :uuid',
                'flash_success' => 'Translation permanently deleted successfully.',
                'flash_warning' => 'Translation not found.',
                'flash_error' => 'An error occurred.',
            ],
        ],
    ],

    // Warehouse staff (pivot `warehouse_users`) — activity messages (WarehouseUserController)
    'warehouseUsers' => [
        'store' => [
            'success' => 'Assigning a staff member to the warehouse: :name',
            'error' => 'Error while assigning the staff member',
            'not_found' => 'User or position not found.',
            'duplicate' => 'This user is already assigned to this warehouse.',
            'flash_success' => 'Staff member (:name) successfully assigned.',
            'flash_error' => 'An error occurred.',
        ],
        'update' => [
            'success' => 'Updating the assignment: :name',
            'warning_meta' => 'Business error while updating the assignment: :uuid',
            'error' => 'Error while updating the assignment: :uuid',
            'not_found' => 'Assignment not found.',
            'flash_success' => 'Assignment (:name) successfully updated.',
            'flash_error' => 'An error occurred.',
        ],
        'destroy' => [
            'success' => 'Removing a staff member: :name',
            'error' => 'Error while removing the staff member: :uuid',
            'not_found' => 'Assignment not found.',
            'flash_success' => 'Staff member (:name) successfully removed.',
            'flash_error' => 'An error occurred.',
        ],
    ],
];
