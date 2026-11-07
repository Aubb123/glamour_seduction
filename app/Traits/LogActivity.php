<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

trait LogActivity
{
    protected function logActivity(
        string $type, // Ex: 'info', 'warning', 'error', 'debug'
        string $action, // Ex: 'User logged in', 'Order created', etc.
        array $context = [], // Contexte additionnel (ex: ['order_id' => 123, 'amount' => 49.99])
        ?string $message = null, // Message optionnel pour plus de détails Ex: 'User John Doe logged in successfully'
        ?string $activity = null, // Clé de traduction i18n du libellé d'activité (résolue à l'affichage via le modèle). Paramètres de remplacement (ex: :uuid) → clé 'i18n_params' dans $context.
        ?bool $is_success = null, // null = non défini (N/A), true = succès, false = échec
        ?Model $loggable = null, // Entité polymorphe optionnelle liée à ce log
    ): void {

        // ── Valider le type ─────────────────────────────────────────
        $validTypes = ['info', 'warning', 'error', 'debug'];
        $type = in_array($type, $validTypes) ? $type : 'info';

        // ── Fichier log (storage/logs) ──────────────────────────────
        Log::{$type}($action, array_merge(
            ['user' => auth()->id()],
            $context
        ));

        // ── Base de données ─────────────────────────────────────────
        try {

            // La colonne `message` est un VARCHAR(255) : un message d'exception long
            // ferait échouer l'INSERT silencieusement (capturé par le catch ci-dessous),
            // rendant l'erreur INVISIBLE dans le dashboard. On tronque donc le message
            // stocké et on conserve l'original complet dans le contexte.
            $storedMessage = $message;

            if ($storedMessage !== null && mb_strlen($storedMessage) > 255) {
                $context['full_message'] = $storedMessage;
                $storedMessage = mb_substr($storedMessage, 0, 252).'...';
            }

            // Utiliser le modèle Eloquent pour créer une entrée dans la table activity_logs
            ActivityLog::create([
                'user_id' => auth()->id(),
                'loggable_type' => $loggable ? get_class($loggable) : null,
                'loggable_id' => $loggable?->getKey(),
                'type' => $type,
                'action' => $action,
                'message' => $storedMessage,
                'context' => $context,
                'activity' => $activity,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'is_success' => $is_success, // null = non défini (N/A), true = succès, false = échec
            ]);

        } catch (\Exception $e) {

            // Ne jamais laisser le log DB planter l'action principale
            Log::error('activity_log.store.error', [
                'message' => $e->getMessage(),
                'action' => $action,
            ]);

        }
    }
}
