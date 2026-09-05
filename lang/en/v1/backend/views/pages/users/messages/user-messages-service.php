<?php

return [
    'users' => [
        'activate' => [
            'already_active' => 'This user is already active.',
            'banned_cannot_activate' => 'Cannot reactivate a banned user.',
        ],
        'suspend' => [
            'already_suspended' => 'This user is already suspended.',
            'only_active_can_suspend' => 'Only active users can be suspended.',
        ],
        'ban' => [
            'already_banned' => 'This user is already banned.',
        ],
        'update' => [
            'cloud_platform_uuid_required' => 'A cloud platform UUID is required to upload a profile image.',
            'cloud_platform_inactive' => 'The selected cloud platform is inactive.',
            'cloud_platform_not_supported' => 'The selected cloud platform is not supported for this upload.',
            'cloud_platform_folder_not_found' => 'No cloud folder was found for this entity and file type.',
        ],
    ],
];
