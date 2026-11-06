<?php

return [
    'shops' => [
        'not_found' => 'Attempt to access a missing shop (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying the shops list',
            'error' => 'Error while displaying the shops list',
        ],
        'create' => [
            'success' => 'Access to the shop creation page',
            'error' => 'Error while accessing the shop creation page',
        ],
        'store' => [
            'success' => 'Creating a new shop: :name',
            'error' => 'Error while creating the shop',
            'flash_success' => 'Shop (:name) successfully created.',
            'flash_error' => 'An error occurred.',
        ],
        'show' => [
            'success' => 'Viewing shop details: :name',
            'warning' => 'Attempt to view a missing shop: :uuid',
            'error' => 'Error while viewing the shop: :uuid',
        ],
        'audits' => [
            'success' => 'Viewing shop audits: :name',
            'error' => 'Error while viewing the shop audits: :uuid',
        ],
        'activity-logs' => [
            'success' => 'Viewing shop activity logs: :name',
            'error' => 'Error while viewing the shop activity logs: :uuid',
        ],
        'edit' => [
            'success' => 'Access to the shop edit page: :name',
            'error' => 'Error while accessing the shop edit page: :uuid',
        ],
        'update' => [
            'success' => 'Updating the shop: :name',
            'warning_meta' => 'Business error while updating the shop: :uuid',
            'error' => 'Error while updating the shop: :uuid',
            'flash_success' => 'Shop (:name) successfully updated.',
            'flash_error' => 'An error occurred.',
        ],
        'activate' => [
            'success' => 'Activating the shop: :name',
            'warning_meta' => 'Business error while activating the shop: :uuid',
            'error' => 'Error while activating the shop: :uuid',
            'flash_success' => 'Shop (:name) successfully activated.',
            'flash_error' => 'An error occurred.',
        ],
        'deactivate' => [
            'success' => 'Deactivating the shop: :name',
            'warning_meta' => 'Business error while deactivating the shop: :uuid',
            'error' => 'Error while deactivating the shop: :uuid',
            'flash_success' => 'Shop (:name) successfully deactivated.',
            'flash_error' => 'An error occurred.',
        ],
        'destroy' => [
            'success' => 'Deleting the shop: :name',
            'warning_meta' => 'Business error while deleting the shop: :uuid',
            'error' => 'Error while deleting the shop: :uuid',
            'flash_success' => 'Shop (:name) successfully deleted.',
            'flash_error' => 'An error occurred.',
        ],
        'trashed' => [
            'success' => 'Displaying the deleted shops list',
            'error' => 'Error while displaying the deleted shops list',
        ],
        'restore' => [
            'success' => 'Restoring the shop: :name',
            'warning' => 'Attempt to restore a missing shop: :uuid',
            'warning_meta' => 'Business error while restoring the shop: :uuid',
            'error' => 'Error while restoring the shop: :uuid',
            'flash_success' => 'Shop (:name) successfully restored.',
            'flash_warning' => 'Shop not found.',
            'flash_error' => 'An error occurred.',
        ],
        'force_destroy' => [
            'success' => 'Permanently deleting the shop: :name',
            'warning' => 'Attempt to permanently delete a missing shop: :uuid',
            'warning_meta' => 'Business error while permanently deleting the shop: :uuid',
            'error' => 'Error while permanently deleting the shop: :uuid',
            'flash_success' => 'Shop successfully deleted permanently.',
            'flash_warning' => 'Shop not found.',
            'flash_error' => 'An error occurred.',
        ],
        'translations' => [
            'destroy' => [
                'success' => 'Deleting the translation: :uuid',
                'warning' => 'Attempt to delete a missing translation: :uuid',
                'warning_meta' => 'Business error while deleting the translation: :uuid',
                'error' => 'Error while deleting the translation: :uuid',
                'flash_success' => 'Translation successfully deleted.',
                'flash_warning' => 'Translation not found.',
                'flash_error' => 'An error occurred.',
            ],
            'trashed' => [
                'accessed' => 'Displaying the shop translations trash: :name',
                'not_found' => 'Shop not found (UUID: :uuid).',
                'error' => 'Error while displaying the translations trash: :uuid',
            ],
            'restore' => [
                'success' => 'Restoring the translation: :uuid',
                'warning' => 'Attempt to restore a missing translation: :uuid',
                'warning_meta' => 'Business error while restoring the translation: :uuid',
                'error' => 'Error while restoring the translation: :uuid',
                'flash_success' => 'Translation successfully restored.',
                'flash_warning' => 'Translation not found.',
                'flash_error' => 'An error occurred.',
            ],
            'force_destroy' => [
                'success' => 'Permanently deleting the translation: :uuid',
                'warning' => 'Attempt to permanently delete a missing translation: :uuid',
                'warning_meta' => 'Business error while permanently deleting the translation: :uuid',
                'error' => 'Error while permanently deleting the translation: :uuid',
                'flash_success' => 'Translation successfully deleted permanently.',
                'flash_warning' => 'Translation not found.',
                'flash_error' => 'An error occurred.',
            ],
        ],
    ],
];
