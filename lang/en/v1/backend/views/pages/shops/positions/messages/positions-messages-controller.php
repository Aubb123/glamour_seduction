<?php

return [
    'positions' => [
        'not_found' => 'Attempt to access a missing position (UUID: :uuid).',
        'index' => ['success' => 'Displaying the positions list', 'error' => 'Error while displaying the positions list'],
        'create' => ['success' => 'Access to the position creation page', 'error' => 'Error while accessing the position creation page'],
        'store' => ['success' => 'Creating a new position: :name', 'error' => 'Error while creating the position', 'flash_success' => 'Position (:name) successfully created.', 'flash_error' => 'An error occurred.'],
        'show' => ['success' => 'Viewing position details: :name', 'warning' => 'Attempt to view a missing position: :uuid', 'error' => 'Error while viewing the position: :uuid'],
        'audits' => ['success' => 'Viewing position audits: :name', 'error' => 'Error while viewing the position audits: :uuid'],
        'activity-logs' => ['success' => 'Viewing position activity logs: :name', 'error' => 'Error while viewing the position activity logs: :uuid'],
        'edit' => ['success' => 'Access to the position edit page: :name', 'error' => 'Error while accessing the position edit page: :uuid'],
        'update' => ['success' => 'Updating the position: :name', 'warning_meta' => 'Business error while updating the position: :uuid', 'error' => 'Error while updating the position: :uuid', 'flash_success' => 'Position (:name) successfully updated.', 'flash_error' => 'An error occurred.'],
        'activate' => ['success' => 'Activating the position: :name', 'warning_meta' => 'Business error while activating the position: :uuid', 'error' => 'Error while activating the position: :uuid', 'flash_success' => 'Position (:name) successfully activated.', 'flash_error' => 'An error occurred.'],
        'deactivate' => ['success' => 'Deactivating the position: :name', 'warning_meta' => 'Business error while deactivating the position: :uuid', 'error' => 'Error while deactivating the position: :uuid', 'flash_success' => 'Position (:name) successfully deactivated.', 'flash_error' => 'An error occurred.'],
        'destroy' => ['success' => 'Deleting the position: :name', 'warning_meta' => 'Business error while deleting the position: :uuid', 'error' => 'Error while deleting the position: :uuid', 'flash_success' => 'Position (:name) successfully deleted.', 'flash_error' => 'An error occurred.'],
        'trashed' => ['success' => 'Displaying the deleted positions list', 'error' => 'Error while displaying the deleted positions list'],
        'restore' => ['success' => 'Restoring the position: :name', 'warning' => 'Attempt to restore a missing position: :uuid', 'warning_meta' => 'Business error while restoring the position: :uuid', 'error' => 'Error while restoring the position: :uuid', 'flash_success' => 'Position (:name) successfully restored.', 'flash_warning' => 'Position not found.', 'flash_error' => 'An error occurred.'],
        'force_destroy' => ['success' => 'Permanently deleting the position: :name', 'warning' => 'Attempt to permanently delete a missing position: :uuid', 'warning_meta' => 'Business error while permanently deleting the position: :uuid', 'error' => 'Error while permanently deleting the position: :uuid', 'flash_success' => 'Position successfully deleted permanently.', 'flash_warning' => 'Position not found.', 'flash_error' => 'An error occurred.'],
        'translations' => [
            'destroy' => ['success' => 'Deleting the translation: :uuid', 'warning' => 'Attempt to delete a missing translation: :uuid', 'warning_meta' => 'Business error while deleting the translation: :uuid', 'error' => 'Error while deleting the translation: :uuid', 'flash_success' => 'Translation successfully deleted.', 'flash_warning' => 'Translation not found.', 'flash_error' => 'An error occurred.'],
            'trashed' => ['accessed' => 'Displaying the position translations trash: :name', 'not_found' => 'Position not found (UUID: :uuid).', 'error' => 'Error while displaying the translations trash: :uuid'],
            'restore' => ['success' => 'Restoring the translation: :uuid', 'warning' => 'Attempt to restore a missing translation: :uuid', 'warning_meta' => 'Business error while restoring the translation: :uuid', 'error' => 'Error while restoring the translation: :uuid', 'flash_success' => 'Translation successfully restored.', 'flash_warning' => 'Translation not found.', 'flash_error' => 'An error occurred.'],
            'force_destroy' => ['success' => 'Permanently deleting the translation: :uuid', 'warning' => 'Attempt to permanently delete a missing translation: :uuid', 'warning_meta' => 'Business error while permanently deleting the translation: :uuid', 'error' => 'Error while permanently deleting the translation: :uuid', 'flash_success' => 'Translation successfully deleted permanently.', 'flash_warning' => 'Translation not found.', 'flash_error' => 'An error occurred.'],
        ],
    ],
];
