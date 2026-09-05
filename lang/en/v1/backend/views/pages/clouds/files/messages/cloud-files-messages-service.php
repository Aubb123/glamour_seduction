<?php

return [
    'cloud-files' => [
        'sync' => [
            'max_selection_exceeded' => 'The maximum number of selected files for synchronization has been exceeded. Please select up to :max files at a time.',
        ],
        'destroy' => [
            'processing_forbidden' => 'The selected file is currently being processed and cannot be deleted.',
            'no_delete_forbidden' => 'The selected file cannot be deleted because it is linked to a model (fileable).',
            'already_deleted' => 'The selected file has already been deleted.',
        ],
        'restore' => [
            'fileable_not_found' => 'The model/entity to which the cloud file is linked (fileable) no longer exists and cannot be restored.',
            'not_trashed_forbidden' => 'The selected file is not in the trash and cannot be restored.',
        ],
        'force_destroy' => [
            'fileable_not_found' => 'Permanent deletion of the cloud file is only possible from the model/entity to which it is linked (fileable).',
            'processing_forbidden' => 'The selected file is currently being processed and cannot be permanently deleted.',
            'no_delete_forbidden' => 'The selected file cannot be permanently deleted because it is linked to a model (fileable).',
            'not_trashed_forbidden' => 'The selected file is not in the trash and cannot be permanently deleted.',
        ],
    ],
];
