<?php

return [

    'country_id' => [
        'required' => 'Le pays de rattachement est obligatoire.',
        'integer' => 'L\'identifiant du pays doit être un entier.',
        'exists' => 'Le pays sélectionné n\'existe pas.',
    ],

    'division_type_id' => [
        'required' => 'Le type de division est obligatoire.',
        'integer' => 'L\'identifiant du type de division doit être un entier.',
        'exists' => 'Le type de division sélectionné n\'existe pas.',
        'inactive' => 'Le type de division sélectionné est inactif et ne peut pas être utilisé.',
    ],

    'parent_id' => [
        'integer' => 'L\'identifiant de la division parente doit être un entier.',
        'exists' => 'La division parente sélectionnée n\'existe pas.',
        'other_country' => 'La division parente doit appartenir au même pays.',
        'self' => 'Une division ne peut pas être sa propre parente.',
        'cycle' => 'La division parente sélectionnée créerait une boucle hiérarchique.',
    ],

    'status' => [
        'required' => 'Le statut est obligatoire.',
        'in' => 'Le statut sélectionné n\'est pas valide.',
    ],

    'notes' => [
        'string' => 'Les notes doivent être une chaîne de caractères.',
        'max' => 'Les notes ne peuvent pas dépasser 2000 caractères.',
    ],

    'translations' => [
        'required' => 'Au moins une traduction est obligatoire.',
        'array' => 'Les traductions doivent être un tableau.',
        'min' => 'Au moins une traduction est obligatoire.',

        'lang_id' => [
            'required' => 'La langue de la traduction est obligatoire.',
            'integer' => 'L\'identifiant de la langue doit être un entier.',
            'exists' => 'La langue sélectionnée n\'existe pas.',
        ],

        'name' => [
            'required' => 'Le nom de la division est obligatoire.',
            'string' => 'Le nom de la division doit être une chaîne de caractères.',
            'max' => 'Le nom de la division ne peut pas dépasser 150 caractères.',
        ],

        'description' => [
            'string' => 'La description doit être une chaîne de caractères.',
        ],

        'duplicate_lang' => 'Une langue ne peut pas être renseignée plusieurs fois dans les traductions.',
    ],

];