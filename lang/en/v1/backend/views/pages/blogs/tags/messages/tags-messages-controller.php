<?php

return [
    'tags' => [
        'not_found' => 'Attempt to access a non-existent blog tag (UUID: :uuid).',
        'index' => [
            'success' => 'Displaying the list of blog tags',
            'error' => 'Error displaying the list of blog tags',
        ],

        'create' => [
            'success' => 'Access to the blog tag creation page',
            'error' => 'Error accessing the blog tag creation page',
        ],

        'store' => [
            'success' => 'Creation of a new blog tag: :name',
            'error' => 'Error creating a blog tag',
            'flash_success' => 'Tag (:name) created successfully.',
            'flash_error' => 'An error occurred while creating the tag.',
        ],

        'show' => [
            'no_name' => 'No name found',
            'success' => 'Viewing details of tag: :name',
            'warning' => 'Attempt to view a non-existent tag: :uuid',
            'error' => 'Error viewing tag: :uuid',
            'slug_mismatch' => 'The blog tag slug does not match the expected slug.',
        ],

        'posts' => [
            'no_name' => 'No name found',
            'success' => 'Viewing tag posts: :name',
            'warning' => 'Attempt to view posts of a non-existent tag: :uuid',
            'error' => 'Error viewing tag posts: :uuid',
            'slug_mismatch' => 'The blog tag slug does not match the expected slug.',
        ],

        // ── AUDITS ──
        'audits' => [
            'success' => 'Viewing tag audits: :name',
            'warning' => 'Attempt to view audits of a non-existent tag: :uuid',
            'error' => 'Error viewing tag audits: :uuid',
            'slug_mismatch' => 'The blog tag slug does not match the expected slug.',
        ],

        // ── ACTIVITY LOGS ──
        'activity-logs' => [
            'success' => 'Viewing tag activity logs: :name',
            'warning' => 'Attempt to view activity logs of a non-existent tag: :uuid',
            'error' => 'Error viewing tag activity logs: :uuid',
            'slug_mismatch' => 'The blog tag slug does not match the expected slug.',
        ],

        'edit' => [
            'success' => 'Access to the tag editing page: :name',
            'warning' => 'Attempt to edit a non-existent tag: :uuid',
            'error' => 'Error accessing the tag editing page: :uuid',
        ],

        'update' => [
            'success' => 'Tag updated in database: :name',
            'warning' => 'Attempt to update a non-existent tag: :uuid',
            'warning_meta' => 'Business error while updating tag: :uuid',
            'error' => 'Error updating tag: :uuid',
            'flash_success' => 'Tag (:name) updated successfully.',
            'flash_warning' => 'Tag not found.',
            'flash_error' => 'An error occurred while updating the tag.',
        ],

        'activate' => [
            'success' => 'Activation of tag: :name',
            'error' => 'Error activating tag: :uuid',
            'flash_success' => 'Tag (:name) activated successfully.',
            'flash_error' => 'An error occurred while activating the tag.',
            'warning' => 'Attempt to activate a non-existent tag: :uuid',
            'flash_warning' => 'Tag not found.',
            'warning_meta' => 'Business error while activating tag: :uuid',
        ],

        'deactivate' => [
            'success' => 'Deactivation of tag: :name',
            'error' => 'Error deactivating tag: :uuid',
            'flash_success' => 'Tag (:name) deactivated successfully.',
            'flash_error' => 'An error occurred while deactivating the tag.',
            'warning' => 'Attempt to deactivate a non-existent tag: :uuid',
            'flash_warning' => 'Tag not found.',
            'warning_meta' => 'Business error while deactivating tag: :uuid',
        ],

        'destroy' => [
            'success' => 'Tag deleted from database: :name',
            'warning' => 'Attempt to delete a non-existent tag: :uuid',
            'warning_meta' => 'Business error while deleting tag: :uuid',
            'error' => 'Error deleting tag: :uuid',
            'flash_success' => 'Tag (:name) deleted successfully.',
            'flash_warning' => 'Tag not found.',
            'flash_error' => 'An error occurred while deleting the tag.',
            'unknown' => 'Unknown tag',
        ],

        'trashed' => [
            'success' => 'Displaying the list of deleted tags',
            'error' => 'Error displaying the list of deleted tags',
        ],

        'restore' => [
            'success' => 'Restoration of tag: :name',
            'warning' => 'Attempt to restore a non-existent tag: :uuid',
            'warning_meta' => 'Business error while restoring tag: :uuid',
            'error' => 'Error restoring tag: :uuid',
            'flash_success' => 'Tag (:name) restored successfully.',
            'flash_warning' => 'Tag not found.',
            'flash_error' => 'An error occurred while restoring the tag.',
        ],

        'force_destroy' => [
            'success' => 'Permanent deletion of tag: :name',
            'warning' => 'Attempt to permanently delete a non-existent tag: :uuid',
            'warning_meta' => 'Business error while permanently deleting tag: :uuid',
            'error' => 'Error permanently deleting tag: :uuid',
            'flash_success' => 'Tag permanently deleted successfully.',
            'flash_warning' => 'Tag not found.',
            'flash_error' => 'An error occurred while permanently deleting the tag.',
            'unknown' => 'Unknown tag',
        ],

        // ── Translations ──
        'translations' => [
            'not_found' => 'Attempt to access a non-existent blog tag translation (UUID: :uuid).',
            'destroy' => [
                'success' => 'Translation :uuid successfully deleted.',
                'flash_success' => 'The translation has been deleted successfully.',
                'warning' => 'Translation :uuid not found.',
                'flash_warning' => 'Translation not found.',
                'warning_meta' => 'Validation error while deleting translation :uuid.',
                'error' => 'Error deleting translation :uuid.',
                'flash_error' => 'An error occurred while deleting the translation.',
                'last_translation_forbidden' => 'Cannot delete the last translation of the tag.',
            ],
            'trashed' => [
                'accessed' => 'Access to trashed translations of tag :name.',
                'not_found' => 'Tag :uuid not found.',
                'error' => 'Error accessing trashed translations.',
            ],
            'restore' => [
                'success' => 'Translation :uuid successfully restored.',
                'flash_success' => 'The translation has been restored successfully.',
                'warning' => 'Translation :uuid not found.',
                'flash_warning' => 'Translation not found.',
                'warning_meta' => 'Validation error while restoring translation :uuid.',
                'error' => 'Error restoring translation :uuid.',
                'flash_error' => 'An error occurred while restoring the translation.',
                'last_translation_forbidden' => 'Cannot restore the last translation of the tag.',
            ],
            'force_destroy' => [
                'success' => 'Translation :uuid permanently deleted.',
                'flash_success' => 'The translation has been permanently deleted.',
                'warning' => 'Translation :uuid not found.',
                'flash_warning' => 'Translation not found for permanent deletion.',
                'warning_meta' => 'Validation error while permanently deleting translation :uuid.',
                'error' => 'Error permanently deleting translation :uuid.',
                'flash_error' => 'An error occurred while permanently deleting the translation.',
                'last_translation_forbidden' => 'Cannot permanently delete the last translation of the tag.',
            ],
        ],
    ],
];
