<?php
if (!defined('ABSPATH')) exit;

/**
 * Class: Bookshive_Community_Feed
 * Description: Simple social timeline for reader activity (Phase 2).
 */

class Bookshive_Community_Feed {

    public static function init() {
        add_shortcode('bookshive_feed', [__CLASS__, 'render_feed']);
        add_action('bookshive_on_book_completed', [__CLASS__, 'log_activity'], 10, 2);
        add_action('wp_ajax_post_status_update', [__CLASS__, 'ajax_post_status']);
    }

    public static function log_activity($user_id, $book_id) {
        $activity = get_option('bookshive_community_feed', []);
        $activity[] = [
            'user' => Bookshive_Helpers::get_user_name($user_id),
            'book' => get_the_title($book_id),
            'date' => current_time('mysql'),
        ];
        update_option('bookshive_community_feed', array_slice($activity, -50)); // Keep latest 50
    }

    public static function ajax_post_status() {
        check_ajax_referer('bookshive_nonce', 'nonce');
        $text = sanitize_textarea_field($_POST['text']);
        $user = wp_get_current_user();

        $feed = get_option('bookshive_community_feed', []);
        $feed[] = [
            'user' => $user->display_name,
            'text' => $text,
            'date' => current_time('mysql'),
        ];
        update_option('bookshive_community_feed', array_slice($feed, -50));

        wp_send_json_success(['feed' => $feed]);
    }

    public static function render_feed() {
        $feed = get_option('bookshive_community_feed', []);
        ob_start(); ?>
        <div class="bookshive-feed">
            <h3><?php _e('Community Feed', 'bookshive'); ?></h3>
            <form id="feed-post-form">
                <textarea name="status" placeholder="Share your reading thoughts..."></textarea>
                <button class="button-primary">Post</button>
            </form>
            <ul>
                <?php foreach (array_reverse($feed) as $item): ?>
                    <li>
                        <strong><?php echo esc_html($item['user']); ?></strong>
                        <p><?php echo esc_html($item['text'] ?? 'Finished ' . $item['book']); ?></p>
                        <small><?php echo esc_html(Bookshive_Helpers::format_date($item['date'])); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }
}

Bookshive_Community_Feed::init();
