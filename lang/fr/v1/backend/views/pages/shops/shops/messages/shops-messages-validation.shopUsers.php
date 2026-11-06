<?php

return [
    'shopUsers' => [
        'user_uuid' => ['required' => 'L\'utilisateur est obligatoire.', 'exists' => 'L\'utilisateur sélectionné est introuvable.'],
        'position_uuid' => ['exists' => 'Le poste sélectionné est introuvable.'],
        'is_manager' => ['boolean' => 'Le champ "responsable" doit être un booléen.'],
    ],
];
