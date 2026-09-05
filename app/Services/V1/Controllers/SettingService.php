<?php

namespace App\Services\V1\Controllers;

use App\Models\Setting;
use App\Models\SettingTranslation;
use Illuminate\Support\Facades\DB;

class SettingService
{
    // ─────────────────────────────────────────────
    // STORE / UPDATE (selon si le setting existe déjà)
    // ─────────────────────────────────────────────
    public function save(array $data): Setting
    {
        return DB::transaction(function () use ($data) {

            $setting = Setting::updateOrCreate(
                ['key' => $data['key']],
                [
                    'group' => $data['group'],
                    'value' => $data['value'],
                    'type' => $data['type'] ?? 'string',
                    'is_public' => $data['is_public'] ?? false,
                    'is_translatable' => $data['is_translatable'] ?? false,
                ]
            );

            // Si le setting a des traductions et qu'on en fournit
            if (isset($data['translations']) && $setting->is_translatable) {
                foreach ($data['translations'] as $translation) {
                    SettingTranslation::updateOrCreate(
                        [
                            'setting_id' => $setting->id,
                            'lang_id' => $translation['lang_id'],
                        ],
                        [
                            'value' => $translation['value'],
                        ]
                    );
                }
            }

            return $setting;
        });
    }

    // ─────────────────────────────────────────────
    // UPDATE  (met à jour la valeur d'un setting existant)
    // ─────────────────────────────────────────────
    public function updateValue(Setting $setting, array $data): Setting
    {
        return DB::transaction(function () use ($setting, $data) {

            $setting->update([
                'value' => $data['value'] ?? $setting->value,
                'is_public' => $data['is_public'] ?? $setting->is_public,
            ]);

            // Mise à jour des traductions si le setting est traduisible
            if ($setting->is_translatable && isset($data['translations'])) {
                foreach ($data['translations'] as $translation) {
                    SettingTranslation::updateOrCreate(
                        [
                            'setting_id' => $setting->id,
                            'lang_id' => $translation['lang_id'],
                        ],
                        [
                            'value' => $translation['value'],
                        ]
                    );
                }
            }

            return $setting;
        });
    }
}
