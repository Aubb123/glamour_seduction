<?php

return [
    'permissions' => [
        'name' => [
            'required' => 'The permission name is required.',
            'string' => 'The permission name must be a string.',
            'max' => 'The permission name may not be greater than 255 characters.',
            'unique' => 'This permission name already exists.',
            'regex' => 'The permission name must only contain lowercase letters, numbers, hyphens, underscores and dots.',
        ],
        'guard_name' => [
            'required' => 'The guard name is required.',
            'string' => 'The guard name must be a string.',
        ],
        'status' => [
            'required' => 'The status is required.',
            'in' => 'The selected status is not valid.',
        ],
        'roles' => [
            'array' => 'The roles must be an array.',
            'exists' => 'One of the selected roles does not exist.',
        ],
    ],
];
