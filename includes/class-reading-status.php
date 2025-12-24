<?php
if (!defined('ABSPATH')) exit;

/**
 * Class: Bookshive_Reading_Status
 * Description: Handles reading status updates (Reading, Paused, DNF, Completed)
 *              and optional reason tracking for Paused / DNF.
 */

class Bookshive_Reading_Status {

    public static function init() {
        add_action('wp_ajax_bookshive_update_reading_status', [__CLASS__, 'update_status']);
        add_action('wp_ajax_nopriv_bookshive_update_reading_status', [__CLASS__, 'update_status']);
    }

    /**
     * Update the reading status for a user's book
     */
    public static function update_status() {
        check_ajax_referer('bookshive_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(__('You must be logged in to update your reading status.', 'bookshive'));
        }

        global $wpdb;
        $table = $wpdb->prefix . 'personal_library_user_books';

        $user_id = get_current_user_id();
        $book_id = intval($_POST['book_id'] ?? 0);
        $status  = sanitize_text_field($_POST['status'] ?? '');
        $reason  = sanitize_textarea_field($_POST['reason'] ?? '');

        $valid_statuses = ['reading', 'paused', 'did_not_finish', 'completed'];

        if (!$book_id || !in_array($status, $valid_statuses, true)) {
            wp_send_json_error(__('Invalid book or status provided.', 'bookshive'));
        }

        // Update the database
        $result = $wpdb->update(
            $table,
            [
                'reading_status' => $status,
                'personal_notes' => $reason ? $reason : null,
                'date_started'   => ($status === 'reading') ? current_time('mysql') : null,
                'date_finished'  => ($status === 'completed') ? current_time('mysql') : null
            ],
            [
                'user_id' => $user_id,
                'book_id' => $book_id
            ],
            ['%s', '%s', '%s', '%s'],
            ['%d', '%d']
        );

        if ($result !== false) {
            wp_send_json_success(__('Reading status updated successfully.', 'bookshive'));
        } else {
            wp_send_json_error(__('Failed to update status. Please try again.', 'bookshive'));
        }
    }
}

Bookshive_Reading_Status::init();
