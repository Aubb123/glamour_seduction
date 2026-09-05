<?php

return [
    'users' => [
        'email' => [
            'prohibited' => 'The email address cannot be changed.',
        ],
        'first_name' => [
            'required' => 'The first name field is required.',
            'string' => 'The first name must be a string.',
            'max' => 'The first name may not be greater than 255 characters.',
        ],
        'last_name' => [
            'required' => 'The last name field is required.',
            'string' => 'The last name must be a string.',
            'max' => 'The last name may not be greater than 255 characters.',
        ],
        'gender' => [
            'required' => 'The gender field is required.',
            'in' => 'The gender must be male or female.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is invalid.',
        ],
        'country_id' => [
            'required' => 'The country field is required.',
            'exists' => 'The selected country does not exist.',
        ],
        'password' => [
            'string' => 'The password must be a string.',
            'min' => 'The password must be at least 8 characters.',
        ],
        'roles' => [
            'required' => 'The roles field is required.',
            'array' => 'The roles must be an array.',
            'exists' => 'A selected role does not exist.',
        ],
        'cloud_platform_uuid' => [
            'string' => 'The cloud platform UUID must be a string.',
            'exists' => 'The selected cloud platform does not exist.',
        ],
        'image' => [
            'image' => 'The image must be a valid image file.',
            'mimes' => 'The image must be a file of type: jpg, png, webp.',
            'max' => 'The image may not be greater than 2 MB.',
        ],
    ],
];
