<?php

return [
    'files' => [
        'no_disk_name' => 'Cloud platform :platform is missing a disk name.',
        'unsupported_platform' => 'Cloud platform :platform is not supported (no implemented driver).',
        'extension_not_allowed' => 'The extension .:extension is not allowed in the folder « :folder ».',
        'mime_mismatch' => 'The file extension (.:extension) does not match the expected type (:type).',
        'upload_failed' => 'The file upload to cloud storage failed.',
        'migrate_error' => 'An error occurred while migrating the file to cloud storage.',
        'duplicate_file' => 'An identical file already exists in this folder. The duplicate was rejected.',
        'platform_not_found' => 'The cloud platform of the file (UUID: :uuid) was not found, or its folder is missing.',
    ],
];
