<?php

return [
    'cloud-platforms' => [
        'store' => [
            'invalid_name' => 'The cloud platform name is invalid. Valid names are: backblaze_b2.',
        ],
        'update' => [
            'invalid_name' => 'The cloud platform name is invalid. Valid names are: backblaze_b2.',
        ],
        'destroy' => [
            'already_deleted' => 'This platform is already deleted and cannot be deleted again.',
            'has_folders' => 'Cannot delete this platform because it contains folders. Please delete the associated folders first.',
            'status_active_forbidden' => 'Cannot delete this platform because it is currently active. Please deactivate it first.',
        ],
        'force_destroy' => [
            'has_folders' => 'Cannot delete this platform because it contains folders. Please delete the associated folders first.',
            'status_active_forbidden' => 'Cannot delete this platform because it is currently active. Please deactivate it first.',
            'not_trashed_forbidden' => 'Only trashed platforms can be permanently deleted.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only trashed platforms can be restored.',
        ],
        'architecture_store' => [
            'success' => 'Created cloud platform architecture: :name',
            'error' => 'Error creating architecture for cloud platform: :uuid',
            'inactive_platform' => 'Cloud platform :name is inactive. Please activate it before creating the architecture.',
        ],
    ],
];
