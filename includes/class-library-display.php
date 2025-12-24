<?php
if (!defined('ABSPATH')) exit;

/**
 * Class: Bookshive_Library_Display
 * Description: Controls the main user library view — filtering, layout, and sorting.
 */

class Bookshive_Library_Display {

    public static function render($atts = []) {
        ob_start();

        $user_id = get_current_user_id();
        $atts = shortcode_atts([
            'layout' => 'grid',
            'show_filters' => 'true'
        ], $atts);

        wp_enqueue_script('bookshive-library');
        wp_enqueue_style('bookshive-library');

        $books = self::get_user_books($user_id);

        include BOOKSHIVE_PATH . 'templates/user-library-display.php';
        return ob_get_clean();
    }

    /** 
     * Fetch user books with filters.
     */
    public static function get_user_books($user_id, $filters = []) {
        global $wpdb;
        $table = $wpdb->prefix . 'personal_library_user_books';

        $query = "SELECT * FROM $table WHERE user_id = %d";
        $params = [$user_id];

        if (!empty($filters['genre'])) {
            $query .= " AND genre = %s";
            $params[] = sanitize_text_field($filters['genre']);
        }

        if (!empty($filters['author'])) {
            $query .= " AND author LIKE %s";
            $params[] = '%' . sanitize_text_field($filters['author']) . '%';
        }

        if (!empty($filters['rating'])) {
            $query .= " AND personal_rating >= %d";
            $params[] = intval($filters['rating']);
        }

        if (!empty($filters['spice'])) {
            $query .= " AND spicy_rating >= %d";
            $params[] = intval($filters['spice']);
        }

        $query .= " ORDER BY date_added DESC";
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }

    /**
     * Handle AJAX filter requests.
     */
    public static function ajax_filter_books() {
        check_ajax_referer('bookshive_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error('Login required.');
        }

        $filters = [
            'genre'  => $_POST['genre'] ?? '',
            'author' => $_POST['author'] ?? '',
            'rating' => $_POST['rating'] ?? '',
            'spice'  => $_POST['spice'] ?? '',
        ];

        $user_id = get_current_user_id();
        $books = self::get_user_books($user_id, $filters);

        ob_start();
        foreach ($books as $book) {
            include BOOKSHIVE_PATH . 'templates/partials/library-book-card.php';
        }
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }

    /**
     * Register AJAX handlers.
     */
    public static function register_ajax() {
        add_action('wp_ajax_bookshive_filter_books', [__CLASS__, 'ajax_filter_books']);
        add_action('wp_ajax_nopriv_bookshive_filter_books', [__CLASS__, 'ajax_filter_books']);
    }
}

Bookshive_Library_Display::register_ajax();
