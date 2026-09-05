<?php

return [
    'cloud-platforms' => [
        'name' => [
            'required' => 'The name field is required.',
            'string' => 'The name must be a string.',
            'max' => 'The name may not be greater than :max characters.',
        ],
        'label' => [
            'required' => 'The label field is required.',
            'string' => 'The label must be a string.',
            'max' => 'The label may not be greater than :max characters.',
        ],
        'api_endpoint' => [
            'required' => 'The API endpoint field is required.',
            'string' => 'The API endpoint must be a string.',
            'max' => 'The API endpoint may not be greater than :max characters.',
            'url' => 'The API endpoint must be a valid URL.',
        ],
        'api_key' => [
            'string' => 'The API key must be a string.',
            'max' => 'The API key may not be greater than :max characters.',
            'unique' => 'This API key is already in use.',
        ],
        'api_secret' => [
            'string' => 'The API secret must be a string.',
            'max' => 'The API secret may not be greater than :max characters.',
        ],
        'disk_name' => [
            'required' => 'The disk name is required.',
            'string' => 'The disk name must be a string.',
            'max' => 'The disk name may not be greater than :max characters.',
            'in' => 'The selected disk name is invalid.',
        ],
        'description' => [
            'string' => 'The description must be a string.',
        ],
        'metadata' => [
            'json' => 'The metadata must be a valid JSON.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'not_found' => 'The cloud platform was not found.',
    ],
];
