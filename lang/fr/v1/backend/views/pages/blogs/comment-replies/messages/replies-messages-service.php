<?php

return [
    'replies' => [
        'activate' => [
            'already_approved' => 'Cette réponse est déjà approuvée.',
        ],
        'deactivate' => [
            'already_disapproved' => 'Cette réponse est déjà désapprouvée.',
        ],
        'destroy' => [
            'visible_forbidden' => 'Impossible de supprimer une réponse approuvée. Veuillez d\'abord la désapprouver.',
            'already_deleted' => 'Cette réponse est déjà supprimée et ne peut plus être supprimée à nouveau.',
        ],
        'restore' => [
            'not_trashed_forbidden' => 'Seules les réponses déjà supprimées ou en corbeille peuvent être restaurées.',
        ],
        'force_destroy' => [
            'not_trashed_forbidden' => 'Seules les réponses déjà supprimées ou en corbeille peuvent être supprimées définitivement.',
        ],
    ],
];
