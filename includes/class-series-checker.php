<?php
if (!defined('ABSPATH')) exit;

/**
 * Class: Bookshive_Series_Checker
 * Description: Detects missing books in a series within a user's collection,
 *              and provides purchase links from trusted sources.
 */

class Bookshive_Series_Checker {

    public static function init() {
        add_shortcode('bookshive_series_checker', [__CLASS__, 'render']);
        add_action('wp_ajax_bookshive_check_series', [__CLASS__, 'ajax_check_series']);
        add_action('wp_ajax_nopriv_bookshive_check_series', [__CLASS__, 'ajax_check_series']);
    }

    /**
     * Shortcode: [bookshive_series_checker]
     */
    public static function render() {
        ob_start();
        ?>
        <div id="bookshive-series-checker">
            <h2><?php _e('Series Completion Checker', 'bookshive'); ?></h2>
            <p><?php _e('Find missing books from your favorite series and where to get them.', 'bookshive'); ?></p>
            <button id="check-series-btn" class="bookshive-btn"><?php _e('Check My Series', 'bookshive'); ?></button>
            <div id="series-results" class="series-results">
                <p class="loading hidden"><?php _e('Scanning your library...', 'bookshive'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX: Check user's library for incomplete series
     */
    public static function ajax_check_series() {
        check_ajax_referer('bookshive_nonce', 'nonce');

        if (!is_user_logged_in()) {
            wp_send_json_error(__('Please log in to check your series.', 'bookshive'));
        }

        global $wpdb;
        $user_id = get_current_user_id();

        $user_books = $wpdb->prefix . 'personal_library_user_books';
        $books_table = $wpdb->prefix . 'personal_library_books';
        $community_table = $wpdb->prefix . 'personal_library_community_books';

        // Get all series the user owns
        $series_data = $wpdb->get_results($wpdb->prepare("
            SELECT DISTINCT b.series_name, b.series_total_books
            FROM $user_books ub
            INNER JOIN $books_table b ON ub.book_id = b.id
            WHERE ub.user_id = %d AND b.series_name IS NOT NULL AND b.series_name != ''
        ", $user_id));

        if (empty($series_data)) {
            wp_send_json_error(__('No series found in your library.', 'bookshive'));
        }

        $missing_series = [];

        foreach ($series_data as $series) {
            // Count how many of the series books the user owns
            $owned = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM $user_books ub
                INNER JOIN $books_table b ON ub.book_id = b.id
                WHERE ub.user_id = %d AND b.series_name = %s
            ", $user_id, $series->series_name));

            if ($owned < $series->series_total_books) {
                $missing_series[] = [
                    'name' => $series->series_name,
                    'owned' => intval($owned),
                    'total' => intval($series->series_total_books),
                    'missing' => $series->series_total_books - $owned,
                    'links' => self::generate_purchase_links($series->series_name)
                ];
            }
        }

        if (empty($missing_series)) {
            wp_send_json_error(__('All your series are complete! Well done! 🎉', 'bookshive'));
        }

        ob_start();
        foreach ($missing_series as $series) : ?>
            <div class="series-item">
                <h3><?php echo esc_html($series['name']); ?></h3>
                <p><?php printf(__('You own %d of %d books.', 'bookshive'), $series['owned'], $series['total']); ?></p>
                <p><strong><?php echo esc_html($series['missing']); ?></strong> <?php _e('missing book(s)', 'bookshive'); ?></p>
                <div class="purchase-links">
                    <?php foreach ($series['links'] as $store => $url): ?>
                        <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener" class="store-link store-<?php echo esc_attr(strtolower($store)); ?>">
                            <?php echo esc_html($store); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach;
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html]);
    }

    /**
     * Generate purchase links for a given series name
     */
    private static function generate_purchase_links($series_name) {
        $query = urlencode($series_name . ' book series');

        return [
            'Amazon' => "https://www.amazon.co.uk/s?k={$query}",
            'The Works' => "https://www.theworks.co.uk/search?q={$query}",
            'Kobo' => "https://www.kobo.com/gb/en/search?query={$query}",
            'Google Books' => "https://books.google.com/books?q={$query}",
        ];
    }
}

Bookshive_Series_Checker::init();
