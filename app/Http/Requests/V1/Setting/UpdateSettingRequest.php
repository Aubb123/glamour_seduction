<?php

namespace App\Http\Requests\V1\Setting;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

use function __;

class UpdateSettingRequest extends FormRequest
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/settings/messages/settings-messages-validation.settings';

    /**
     * Contraintes de cohérence par clé de setting numérique.
     * Format : [type, min, max].
     *
     * @var array<string, array{0: string, 1: int|float, 2: int|float}>
     */
    private const NUMERIC_RULES = [
        'max_image_size_kb' => ['integer', 1, 10240],
        'max_product_images' => ['integer', 1, 50],
        'min_product_images' => ['integer', 1, 50],
        'max_product_variant_images' => ['integer', 1, 50],
        'max_product_warehouse_notes_length' => ['integer', 1, 10000],
        'max_product_tags' => ['integer', 1, 100],
        'max_blog_post_tags' => ['integer', 1, 100],
        'blog_reading_words_per_minute' => ['integer', 50, 1000],
        'dashboard_latest_items_limit' => ['integer', 1, 50],
        'datatable_default_page_length' => ['integer', 5, 100],
        'activity_logs_retention_days' => ['integer', 1, 3650],
        'audits_retention_days' => ['integer', 1, 3650],
        'min_password_length' => ['integer', 6, 64],
        'max_product_price' => ['numeric', 0.01, 999999999.99],
        'max_product_weight' => ['numeric', 0, 999999.99],
    ];

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // ← géré par middleware
    }

    public function rules(): array
    {
        return [
            // Chaque setting est indexé par son UUID
            'settings' => ['required', 'array', 'min:1'],

            // UUID du setting (doit exister dans la table settings)
            'settings.*.uuid' => ['required', 'string', 'exists:settings,uuid'],

            // Valeur du setting — optionnelle (nullable en BDD)
            'settings.*.value' => ['nullable', 'string'],

            // Visibilité publique — champ caché '0' + checkbox '1' si cochée
            'settings.*.is_public' => ['nullable', 'in:0,1'],

            // Traductions — optionnelles (tableau indexé par lang_id)
            'settings.*.translations' => ['nullable', 'array'],

            // Langue de la traduction — obligatoire si une traduction est fournie
            'settings.*.translations.*.lang_id' => ['required_with:settings.*.translations.*', 'integer', 'exists:langs,id'],

            // Valeur traduite — optionnelle (nullable en BDD)
            'settings.*.translations.*.value' => ['nullable', 'string'],
        ];
    }

    /**
     * Valide la cohérence des valeurs numériques saisies.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $settings = (array) $this->input('settings', []);
            $values = [];
            $uuidByKey = [];

            foreach ($settings as $uuid => $item) {
                if (! is_array($item) || ! array_key_exists('value', $item)) {
                    continue;
                }

                $setting = Setting::where('uuid', $item['uuid'] ?? $uuid)->first();

                if (! $setting) {
                    continue;
                }

                $uuidByKey[$setting->key] = $uuid;

                if (! isset(self::NUMERIC_RULES[$setting->key])) {
                    continue;
                }

                $value = $item['value'];

                // Valeur vide : le paramètre n'est pas renseigné, aucune contrainte de plage.
                if ($value === null || $value === '') {
                    continue;
                }

                [$type, $min, $max] = self::NUMERIC_RULES[$setting->key];

                $isValid = $type === 'integer'
                    ? filter_var($value, FILTER_VALIDATE_INT) !== false
                    : is_numeric($value);

                if (! $isValid) {
                    $validator->errors()->add("settings.{$uuid}.value", __("{$this->backendLangPath}.value.{$type}",['key' => $setting->key]
                    ));

                    continue;
                }

                $numeric = $type === 'integer' ? (int) $value : (float) $value;
                $values[$setting->key] = $numeric;

                if ($numeric < $min || $numeric > $max) {
                    $validator->errors()->add("settings.{$uuid}.value", __(
                        "{$this->backendLangPath}.value.range",
                        ['key' => $setting->key, 'min' => $min, 'max' => $max]
                    ));
                }
            }

            // Cohérence croisée : le minimum d'images produit ne peut pas dépasser le maximum.
            $minImages = $values['min_product_images'] ?? (int) Setting::where('key', 'min_product_images')->value('value');
            $maxImages = $values['max_product_images'] ?? (int) Setting::where('key', 'max_product_images')->value('value');

            if ($minImages > $maxImages) {
                $uuid = $uuidByKey['min_product_images'] ?? 'settings';

                $validator->errors()->add("settings.{$uuid}.value", __(
                    "{$this->backendLangPath}.value.image_range"
                ));
            }
        });
    }

    public function messages(): array
    {
        return [
            'settings.required' => __("{$this->backendLangPath}.settings.required"),
            'settings.array' => __("{$this->backendLangPath}.settings.array"),
            'settings.min' => __("{$this->backendLangPath}.settings.min"),

            'settings.*.uuid.required' => __("{$this->backendLangPath}.uuid.required"),
            'settings.*.uuid.exists' => __("{$this->backendLangPath}.uuid.exists"),

            'settings.*.value.string' => __("{$this->backendLangPath}.value.string"),

            'settings.*.translations.*.lang_id.required_with' => __("{$this->backendLangPath}.translations.lang_id.required"),
            'settings.*.translations.*.lang_id.integer' => __("{$this->backendLangPath}.translations.lang_id.integer"),
            'settings.*.translations.*.lang_id.exists' => __("{$this->backendLangPath}.translations.lang_id.exists"),

            'settings.*.translations.*.value.string' => __("{$this->backendLangPath}.translations.value.string"),
        ];
    }
}
