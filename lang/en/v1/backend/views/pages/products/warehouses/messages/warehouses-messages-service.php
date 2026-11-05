<?php

return [
    'productWarehouses' => [
        'activate' => [
            'already_active' => 'This warehouse is already active.',
        ],
        'deactivate' => [
            'already_inactive' => 'This warehouse is already inactive.',
            'default_forbidden' => 'Cannot deactivate the default warehouse. Set another default warehouse first.',
        ],
        'update' => [
            'default_forbidden' => 'Cannot remove the "default" status from the last default warehouse. Set another default warehouse first.',
        ],
        'destroy' => [
            'active_forbidden' => 'Cannot delete an active warehouse. Please deactivate it first.',
            'already_deleted' => 'This warehouse is already deleted and cannot be deleted again.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Only warehouses already deleted or in the trash can be restored.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Only warehouses already deleted or in the trash can be permanently deleted.',
        ],
    ],
];
