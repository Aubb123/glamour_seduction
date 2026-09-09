<?php

return [

    // ─────────────────────────────────────────────
    // CONTINENT
    // ─────────────────────────────────────────────
    'continent_id' => [
        'required' => 'Le continent est obligatoire.',
        'integer' => 'Le continent doit être un entier.',
        'exists' => 'Le continent sélectionné n\'existe pas.',
    ],

    // ─────────────────────────────────────────────
    // TYPE
    // ─────────────────────────────────────────────
    'type' => [
        'required' => 'Le type est obligatoire.',
        'in' => 'Le type doit être une valeur autorisée.',
    ],

    // ─────────────────────────────────────────────
    // STATUT
    // ─────────────────────────────────────────────
    'status' => [
        'required' => 'Le statut est obligatoire.',
        'in' => 'Le statut sélectionné n\'est pas valide.',
    ],

    // ─────────────────────────────────────────────
    // PAYS SOUVERAIN
    // ─────────────────────────────────────────────
    'sovereign_id' => [
        'exists' => 'Le pays souverain sélectionné n\'existe pas.',
        'required_unless' => 'Le pays souverain est obligatoire lorsque le type d\'entité n\'est pas un pays souverain.',
        'prohibited_if' => 'Le pays souverain ne doit pas être renseigné lorsque le type d\'entité est « pays souverain ».',
        'not_in' => 'Un pays ne peut pas être rattaché à lui-même comme pays souverain.',
    ],

    // ─────────────────────────────────────────────
    // CODE ALPHA-3
    // ─────────────────────────────────────────────
    'code' => [
        'string' => 'Le code pays (alpha-3) doit être une chaîne de caractères.',
        'size' => 'Le code pays (alpha-3) doit contenir exactement 3 caractères.',
        'regex' => 'Le code pays (alpha-3) doit contenir uniquement des lettres majuscules (ex: FRA, USA, BEN).',
        'unique' => 'Ce code pays (alpha-3) est déjà utilisé.',
    ],

    // ─────────────────────────────────────────────
    // CODE ALPHA-2
    // ─────────────────────────────────────────────
    'code_2' => [
        'required' => 'Le code pays (alpha-2) est obligatoire.',
        'string' => 'Le code pays (alpha-2) doit être une chaîne de caractères.',
        'max' => 'Le code pays (alpha-2) ne peut pas dépasser 6 caractères.',
        'regex' => 'Le code pays (alpha-2) doit contenir uniquement des lettres majuscules (ex: FR, US, BJ) ou un code étendu de subdivision (ex: GB-ENG).',
        'unique' => 'Ce code pays (alpha-2) est déjà utilisé.',
    ],

    // ─────────────────────────────────────────────
    // INDICATIF TÉLÉPHONIQUE
    // ─────────────────────────────────────────────
    'phone_code' => [
        'string' => 'L\'indicatif téléphonique doit être une chaîne de caractères.',
        'max' => 'L\'indicatif téléphonique ne peut pas dépasser 10 caractères.',
        'regex' => 'L\'indicatif téléphonique doit être au format +XXX ou +X-XXXX (ex: +33, +229, +1-441).',
    ],

    'file_path' => [
        'image' => 'Le fichier image est invalide.',
        'mimes' => 'Le fichier doit être une image au format PNG, JPG ou WEBP.',
        'max' => 'Le fichier image ne peut pas dépasser 2 Mo.',
    ],

    // ─────────────────────────────────────────────
    // ORDRE D'AFFICHAGE
    // ─────────────────────────────────────────────
    'sort_order' => [
        'integer' => 'L\'ordre d\'affichage doit être un entier.',
        'min' => 'L\'ordre d\'affichage doit être supérieur ou égal à 0.',
        'unique' => 'Cet ordre d\'affichage est déjà utilisé par un autre pays.',
    ],

    // ─────────────────────────────────────────────
    // ─────────────────────────────────────────────
    // DEVISE
    // ─────────────────────────────────────────────
    'currency_id' => [
        'integer' => 'La devise doit être un entier.',
        'exists' => 'La devise sélectionnée n\'existe pas.',
    ],

    // ─────────────────────────────────────────────
    // TRADUCTIONS
    // ─────────────────────────────────────────────
    'translations' => [
        'required' => 'Au moins une traduction est obligatoire.',
        'array' => 'Les traductions doivent être un tableau.',
        'min' => 'Au moins une traduction est obligatoire.',

        // Langue
        'lang_id' => [
            'required' => 'La langue de la traduction est obligatoire.',
            'integer' => 'L\'identifiant de la langue doit être un entier.',
            'exists' => 'La langue sélectionnée n\'existe pas.',
        ],

        // Nom
        'name' => [
            'required' => 'Le nom du pays est obligatoire.',
            'string' => 'Le nom du pays doit être une chaîne de caractères.',
            'max' => 'Le nom du pays ne peut pas dépasser 255 caractères.',
        ],

        // Description
        'description' => [
            'required' => 'La description du pays est obligatoire.',
            'string' => 'La description doit être une chaîne de caractères.',
        ],
    ],

];
