<?php

return [
    'contacts' => [
        'destroy' => [
            'flash_warning' => 'Seuls les messages déjà lus peuvent être supprimés.',
            'already_deleted' => 'Ce message est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les messages déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'flash_warning' => 'Seuls les messages déjà lus peuvent être supprimés définitivement.',
            'not_trashed_forbidden' => 'Seuls les messages déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],
    ],

];
