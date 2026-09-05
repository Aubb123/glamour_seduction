<?php

return [
    'comments' => [
        'activate' => [
            'already_approved' => 'Ce commentaire est déjà approuvé.',
        ],
        'deactivate' => [
            'already_disapproved' => 'Ce commentaire est déjà désapprouvé.',
        ],
        'destroy' => [
            'visible_forbidden' => 'Impossible de supprimer un commentaire approuvé. Veuillez d\'abord le désapprouver.',
            'already_deleted' => 'Ce commentaire est déjà supprimé et ne peut plus être supprimé à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seuls les commentaires déjà supprimés ou en corbeille peuvent être restaurés.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seuls les commentaires déjà supprimés ou en corbeille peuvent être supprimés définitivement.',
        ],
    ],
];
