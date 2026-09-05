<?php

return [
    'contacts' => [
        'not_found' => 'Attempt to access a non-existent contact message (UUID: :uuid).',
        'index' => [
            'success' => 'Viewing the contacts list',
            'error' => 'Error viewing the contacts list',
        ],
        'show' => [
            'success' => 'Viewing contact details: :name',
            'warning' => 'Attempt to view a non-existent contact: :uuid',
            'error' => 'Error viewing contact: :uuid',
        ],
        'mark_as_read' => [
            'success' => 'Marking contact as read: :name',
            'warning' => 'Attempt to mark as read a non-existent contact: :uuid',
            'warning_meta' => 'Business error when marking contact as read: :uuid',
            'error' => 'Error marking contact as read: :uuid',
            'flash_success' => 'Message marked as read successfully.',
            'flash_warning' => 'Contact not found.',
            'flash_error' => 'An error occurred.',
        ],
        'mark_as_unread' => [
            'success' => 'Marking contact as unread: :name',
            'warning' => 'Attempt to mark as unread a non-existent contact: :uuid',
            'warning_meta' => 'Business error when marking contact as unread: :uuid',
            'error' => 'Error marking contact as unread: :uuid',
            'flash_success' => 'Message marked as unread successfully.',
            'flash_warning' => 'Contact not found.',
            'flash_error' => 'An error occurred.',
        ],
        'destroy' => [
            'success' => 'Deleting contact from database: :name',
            'warning' => 'Attempt to delete a non-existent contact: :uuid',
            'warning_meta' => 'Business error when deleting contact: :uuid',
            'error' => 'Error deleting contact: :uuid',
            'flash_success' => 'Message (:name) deleted successfully.',
            'flash_warning' => 'Contact not found.',
            'flash_error' => 'An error occurred.',
        ],
        'trashed' => [
            'success' => 'Viewing the deleted contacts list',
            'error' => 'Error viewing the deleted contacts list',
        ],
        'restore' => [
            'success' => 'Restoring contact: :name',
            'warning' => 'Attempt to restore a non-existent contact: :uuid',
            'warning_meta' => 'Business error when restoring contact: :uuid',
            'error' => 'Error restoring contact: :uuid',
            'flash_success' => 'Message (:name) restored successfully.',
            'flash_warning' => 'Contact not found.',
            'flash_error' => 'An error occurred.',
        ],
        'force_destroy' => [
            'success' => 'Permanently deleting contact: :name',
            'warning' => 'Attempt to permanently delete a non-existent contact: :uuid',
            'warning_meta' => 'Business error when permanently deleting contact: :uuid',
            'error' => 'Error permanently deleting contact: :uuid',
            'flash_success' => 'Message permanently deleted successfully.',
            'flash_warning' => 'Contact not found.',
            'flash_error' => 'An error occurred.',
        ],
    ],
];
