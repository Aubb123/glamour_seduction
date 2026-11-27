<?php

return [

    'code' => [
        'required' => 'Le code technique est obligatoire.',
        'string' => 'Le code technique doit être une chaîne de caractères.',
        'max' => 'Le code technique ne peut pas dépasser 50 caractères.',
        'regex' => 'Le code technique doit contenir uniquement des minuscules, chiffres et underscores (ex: department).',
        'unique' => 'Ce code technique est déjà utilisé.',
    ],

    'level' => [
        'required' => 'Le niveau par défaut est obligatoire.',
        'integer' => 'Le niveau par défaut doit être un entier.',
        'min' => 'Le niveau par défaut doit être supérieur ou égal à 1.',
    ],

    'sort_order' => [
        'required' => 'L\'ordre d\'affichage est obligatoire.',
        'integer' => 'L\'ordre d\'affichage doit être un entier.',
        'min' => 'L\'ordre d\'affichage doit être supérieur ou égal à 0.',
    ],

    'status' => [
        'required' => 'Le statut est obligatoire.',
        'in' => 'Le statut sélectionné n\'est pas valide.',
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
            'required' => 'Le libellé du type de division est obligatoire.',
            'string' => 'Le libellé du type de division doit être une chaîne de caractères.',
            'max' => 'Le libellé du type de division ne peut pas dépasser 100 caractères.',
            'unique' => 'Le libellé « :name » est déjà utilisé pour cette langue.',
        ],

        'description' => [
            'required' => 'La description du type de division est obligatoire.',
            'string' => 'La description doit être une chaîne de caractères.',
        ],

        'duplicate_lang' => 'Une langue ne peut pas être renseignée plusieurs fois dans les traductions.',
    ],

];
