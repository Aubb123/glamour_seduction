<?php

namespace App\Services\V1\Controllers;

use App\Models\Contact;
use Illuminate\Support\Facades\DB;

class ContactService
{
    protected string $langPath = 'v1/backend/views/pages/contacts/messages/contacts-messages-service.contacts';

    // ─────────────────────────────────────────────
    // MARK AS READ
    // ─────────────────────────────────────────────
    public function backend_contacts_mark_as_read(Contact $contact): void
    {
        DB::transaction(function () use ($contact) {
            $contact->update(['is_read' => true]);
        });
    }

    // ─────────────────────────────────────────────
    // MARK AS UNREAD
    // ─────────────────────────────────────────────
    public function backend_contacts_mark_as_unread(Contact $contact): void
    {
        DB::transaction(function () use ($contact) {
            $contact->update(['is_read' => false]);
        });
    }

    // ─────────────────────────────────────────────
    // DESTROY (soft delete)
    // ─────────────────────────────────────────────
    public function backend_contacts_destroy(Contact $contact): void
    {
        DB::transaction(function () use ($contact) {

            // Seule les messages déjà lus peuvent être supprimés
            if (!$contact->is_read) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.flash_warning"));
            }

            if ($contact->trashed()) {
                throw new \InvalidArgumentException(__("{$this->langPath}.destroy.already_deleted"));
            }
            destroy_function($contact);
        });
    }

    // ─────────────────────────────────────────────
    // RESTORE
    // ─────────────────────────────────────────────
    public function backend_contacts_restore(Contact $contact): void
    {
        DB::transaction(function () use ($contact) {
            if ($contact->trashed()) {
                restore_function($contact);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.restore.not_trashed_forbidden"));
            }
        });
    }

    // ─────────────────────────────────────────────
    // FORCE DESTROY
    // ─────────────────────────────────────────────
    public function backend_contacts_force_destroy(Contact $contact): void
    {
        DB::transaction(function () use ($contact) {

            // Seule les messages déjà lus peuvent être supprimés définitivement
            if (!$contact->is_read) {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.flash_warning"));
            }

            if ($contact->trashed()) {
                force_destroy_function($contact);
            } else {
                throw new \InvalidArgumentException(__("{$this->langPath}.force_destroy.not_trashed_forbidden"));
            }
        });
    }
}
