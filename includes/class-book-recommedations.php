<?php
if (!defined('ABSPATH')) exit;

/**
 * Class: Bookshive_Book_Recommendations
 * Description: Suggests books based on user's reading trends and genre affinity.
 */

class Bookshive_Book_Recommendations {

    public static function init() {
        add_shortcode('bookshive_recommendations', [__CLASS__, 'render_recommendations']);
        add_action('wp_ajax_bookshive_get_recommendations', [__CLASS__, 'ajax_get_recommendations']);
        add_action('wp_ajax_nopriv_bookshive_get_recommendations', [__CLASS__, 'ajax_get_recommendations']);
    }

    /**
     * Render the shortcode [bookshive_recommendations]
     */
    public static function render_recommendations($atts = []) {
        ob_start();
        wp_enqueue_script('bookshive-recommendations');
        wp_enqueue_style('bookshive-library');

        $user_id = get_current_user_id();

        echo '<div id="bookshive-recommendations" class="bookshive-recommendations">';
        echo '<h2>' . esc_html__('Recommended For You', 'bookshive') . '</h2>';
        echo '<div class="recommendations-container" data-user="' . esc_attr($user_id) . '">';
        echo '<p class="loading">' . esc_html__('Loading your book suggestions...', 'bookshive') . '</p>';
        echo '</div>';
        echo '</div>';

        return ob_get_clean();
    }

    /**
     * AJAX: Generate book recommendations for the current user
     */
    public static function ajax_get_recommendations() {
        check_ajax_referer('bookshive_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(__('You must be logged in to see recommendations.', 'bookshive'));
        }

        global $wpdb;
        $user_id = get_current_user_id();

        $books_table = $wpdb->prefix . 'personal_library_user_books';
        $community_table = $wpdb->prefix . 'personal_library_community_books';
        $reviews_table = $wpdb->prefix . 'personal_library_reviews';

        // Fetch user’s top genres
        $genres = $wpdb->get_col($wpdb->prepare("
            SELECT DISTINCT cb.categories
            FROM $books_table ub
            INNER JOIN {$wpdb->prefix}personal_library_books b ON ub.book_id = b.id
            INNER JOIN $community_table cb ON b.community_book_id = cb.id
            WHERE ub.user_id = %d
            AND ub.reading_status IN ('reading','completed')
            LIMIT 5
        ", $user_id));

        $genres_str = '';
        if (!empty($genres)) {
            $genres_list = implode(',', array_map(fn($g) => sanitize_text_field($g), $genres));
            $genres_str = "AND (cb.categories LIKE '%" . implode("%' OR cb.categories LIKE '%", $genres) . "%')";
        }

        // Recommend books by matching genres and excluding owned books
        $recommendations = $wpdb->get_results($wpdb->prepare("
            SELECT cb.*
            FROM $community_table cb
            WHERE cb.id NOT IN (
                SELECT b.community_book_id
                FROM $books_table ub
                INNER JOIN {$wpdb->prefix}personal_library_books b ON ub.book_id = b.id
                WHERE ub.user_id = %d
            )
            $genres_str
            ORDER BY cb.average_rating DESC, cb.total_reviews DESC
            LIMIT 10
        ", $user_id));

        if (empty($recommendations)) {
            wp_send_json_error(__('No new recommendations found. Try exploring new genres!', 'bookshive'));
        }

        ob_start();
        foreach ($recommendations as $book) {
            include BOOKSHIVE_PATH . 'templates/partials/library-book-card.php';
        }
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }
}

Bookshive_Book_Recommendations::init();
