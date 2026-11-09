<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;

/**
 * Validates translated values against every stored translation (including soft-deleted
 * rows) and against duplicates in the submitted payload.
 */
trait ValidatesTranslatedFields
{
    /**
     * @param  array<int, string>  $fields
     */
    protected function validateTranslatedFields(
        Validator $validator,
        string $table,
        string $parentForeignKey,
        ?int $parentId,
        array $fields,
    ): void {
        $translations = $this->input('translations', []);

        if (! is_array($translations)) {
            return;
        }

        foreach ($fields as $field) {
            $seen = [];

            foreach ($translations as $index => $translation) {
                if (! is_array($translation)) {
                    continue;
                }

                $langId = $translation['lang_id'] ?? null;
                $value = $translation[$field] ?? null;

                // Null/empty optional values are not treated as duplicates.
                if ($langId === null || $value === null || $value === '') {
                    continue;
                }

                $fingerprint = mb_strtolower((string) $langId."\0".(string) $value);

                if (isset($seen[$fingerprint])) {
                    $validator->errors()->add(
                        "translations.{$index}.{$field}",
                        __('validation.unique', ['attribute' => $field]),
                    );
                }

                $seen[$fingerprint] = true;

                // Query the table directly so Eloquent SoftDeletes scopes cannot hide
                // a conflicting row. When updating, find the row to preserve by its
                // trusted parent + language pair; never trust a submitted translation ID.
                $query = DB::table($table)
                    ->where('lang_id', $langId)
                    ->where($field, $value);

                if ($parentId !== null) {
                    $ownTranslationId = DB::table($table)
                        ->where($parentForeignKey, $parentId)
                        ->where('lang_id', $langId)
                        ->value('id');

                    if ($ownTranslationId !== null) {
                        $query->where('id', '!=', $ownTranslationId);
                    }
                }

                if ($query->exists()) {
                    $validator->errors()->add(
                        "translations.{$index}.{$field}",
                        __('validation.unique', ['attribute' => $field]),
                    );
                }
            }
        }
    }
}
