<?php

namespace App\Services\V1\Controllers;

use App\Enums\Users\UserStatus;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    // Chemin de base pour les traductions (centralisé)
    protected string $backendLangPath = 'v1/backend/views/pages/users/messages/user-messages-service.users';

    public function __construct(protected CloudFileService $cloudFileService) {}

    // ─────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────
    public function backend_users_update(User $user, array $data): User
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        return DB::transaction(function () use ($user, $data) {

            // Préparation des données de mise à jour (uniquement les champs fournis)
            $updateData = [];

            if (isset($data['first_name'])) {
                $updateData['first_name'] = $data['first_name'];
            }
            if (isset($data['last_name'])) {
                $updateData['last_name'] = $data['last_name'];
            }
            if (isset($data['gender'])) {
                $updateData['gender'] = $data['gender'];
            }
            if (isset($data['status'])) {
                $updateData['status'] = UserStatus::tryFrom($data['status']) ?? $user->status;
            }
            if (isset($data['country_id'])) {
                $updateData['country_id'] = $data['country_id'];
            }
            if (isset($data['password']) && ! empty($data['password'])) {
                // Hashé automatiquement par le cast 'hashed' du modèle User
                $updateData['password'] = $data['password'];
            }

            // Application de la mise à jour si des données sont présentes
            if (! empty($updateData)) {
                $user->update($updateData);
            }

            // Synchronisation des rôles si fournis
            if (isset($data['roles'])) {
                $roleNames = Role::whereIn('id', $data['roles'])->pluck('name')->toArray();
                $user->syncRoles($roleNames);
            }

            // Si une image est fournie
            if (isset($data['image'])) {

                // Si $data['image'] existe et que $data['cloud_platform_uuid'] n'est pas défini, on lance une exception
                if (! isset($data['cloud_platform_uuid'])) {
                    throw new \InvalidArgumentException(__("{$this->backendLangPath}.update.cloud_platform_uuid_required"));
                }

                // Appel du service pour gérer le téléchargement de l'image vers le cloud ou le stockage local, en associant l'image à l'utilisateur mis à jour
                $this->cloudFileService->uploadEntityImage(
                    $data['image'],
                    $data['cloud_platform_uuid'],
                    $user,
                    $user->getTable(),
                    $this->backendLangPath,
                );

            }

            return $user; // ← retourne l'utilisateur mis à jour
        });
    }

    // ─────────────────────────────────────────────
    // ACTIVATE
    // ─────────────────────────────────────────────
    public function backend_users_activate(User $user): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($user) {

            // Vérification que l'utilisateur n'est pas déjà actif
            if ($user->status === UserStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.already_active"));
            }

            // Un utilisateur banni ne peut pas être activé
            if ($user->status === UserStatus::Banned) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.activate.banned_cannot_activate"));
            }

            // Activation de l'utilisateur (passage en statut 'actif')
            $user->update(['status' => UserStatus::Active]);
        });
    }

    // ─────────────────────────────────────────────
    // SUSPEND
    // ─────────────────────────────────────────────
    public function backend_users_suspend(User $user): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($user) {

            // Vérification que l'utilisateur n'est pas déjà suspendu
            if ($user->status === UserStatus::Suspended) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.suspend.already_suspended"));
            }

            // Seuls les utilisateurs actifs peuvent être suspendus
            if ($user->status !== UserStatus::Active) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.suspend.only_active_can_suspend"));
            }

            // Suspension de l'utilisateur (passage en statut 'suspendu')
            $user->update(['status' => UserStatus::Suspended]);
        });
    }

    // ─────────────────────────────────────────────
    // BAN
    // ─────────────────────────────────────────────
    public function backend_users_ban(User $user): void
    {
        // Utilisation d'une transaction pour garantir l'intégrité des données
        DB::transaction(function () use ($user) {

            // Vérification que l'utilisateur n'est pas déjà banni
            if ($user->status === UserStatus::Banned) {
                throw new \InvalidArgumentException(__("{$this->backendLangPath}.ban.already_banned"));
            }

            // Bannissement de l'utilisateur (passage en statut 'banni')
            $user->update(['status' => UserStatus::Banned]);
        });
    }
}
