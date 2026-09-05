<?php

return [
    'cloud-folders' => [
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'folder_name' => [
            'string' => 'The folder name must be a string.',
        ],
        'entity_table' => [
            'string' => 'The entity table must be a string.',
        ],
        'content_type' => [
            'array' => 'The content type must be an array of extensions.',
        ],
        'not_found' => 'The cloud folder was not found.',
    ],
];
