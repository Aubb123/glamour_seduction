<?php

return [
    'cloud-folders' => [
        'not_found' => 'Attempt to access a non-existent cloud folder (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying cloud folders list',
            'error' => 'Error displaying cloud folders list',
        ],
        'show' => [
            'no_name' => 'No name found',
            'success' => 'Viewing cloud folder details: :name',
            'warning' => 'Attempt to view a non-existent cloud folder: :uuid',
            'error' => 'Error viewing cloud folder: :uuid',
        ],
        'files' => [
            'success' => 'Viewing files of cloud folder: :name',
            'warning' => 'Attempt to view files of a non-existent cloud folder: :uuid',
            'error' => 'Error viewing files of cloud folder: :uuid',
        ],
        'subfolders' => [
            'success' => 'Viewing subfolders of cloud folder: :name',
            'warning' => 'Attempt to view subfolders of a non-existent cloud folder: :uuid',
            'error' => 'Error viewing subfolders of cloud folder: :uuid',
        ],
        'edit' => [
            'success' => 'Access to cloud folder edit page: :name',
            'warning' => 'Attempt to edit a non-existent cloud folder: :uuid',
            'error' => 'Error accessing cloud folder edit page: :uuid',
        ],
        'update' => [
            'success' => 'Updated cloud folder: :name',
            'warning' => 'Attempt to update a non-existent cloud folder: :uuid',
            'warning_meta' => 'Business error while updating cloud folder: :uuid',
            'error' => 'Error updating cloud folder: :uuid',
            'flash_success' => 'Cloud folder (:name) updated successfully.',
            'flash_warning' => 'Cloud folder not found.',
            'flash_error' => 'An error occurred during update.',
        ],
        'toggle_active' => [
            'success_activated' => 'Cloud folder activated: :name',
            'success_deactivated' => 'Cloud folder deactivated: :name',
            'error' => 'Error toggling cloud folder status: :uuid',
            'flash_success_activated' => 'Cloud folder (:name) activated successfully.',
            'flash_success_deactivated' => 'Cloud folder (:name) deactivated successfully.',
            'flash_error' => 'An error occurred while toggling status.',
            'warning' => 'Attempt to toggle status of a non-existent cloud folder: :uuid',
            'warning_meta' => 'Business error while toggling cloud folder status: :uuid',
            'flash_warning' => 'Cloud folder not found.',
        ],
    ],
];
