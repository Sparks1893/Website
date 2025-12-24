
<?php
if (!defined('ABSPATH')) exit;

/**
 * Class: Bookshive_Shop_Shortcodes
 * Description: Handles public-facing shop shortcodes for displaying books and author stores.
 */

class Bookshive_Shop_Shortcodes {

    public static function init() {
        add_shortcode('bookshive_shop', [__CLASS__, 'render_shop']);
        add_shortcode('bookshive_author_store', [__CLASS__, 'render_author_store']);
    }

    /**
     * Shortcode: [bookshive_shop]
     * Displays all published shop books with pagination.
     */
    public static function render_shop($atts = []) {
        $atts = shortcode_atts([
            'posts_per_page' => 12,
            'paged' => max(1, get_query_var('paged'))
        ], $atts);

        ob_start();
        wp_enqueue_style('bookshive-shop');
        wp_enqueue_script('bookshive-shop');

        $args = [
            'post_type' => 'bookshive_shop_book',
            'posts_per_page' => intval($atts['posts_per_page']),
            'paged' => intval($atts['paged']),
            'post_status' => 'publish'
        ];

        $query = new WP_Query($args);

        echo '<div class="bookshive-shop-container">';
        echo '<h2>' . esc_html__('Bookshive Marketplace', 'bookshive') . '</h2>';

        if ($query->have_posts()) {
            echo '<div class="bookshive-grid">';
            while ($query->have_posts()) : $query->the_post();
                include BOOKSHIVE_PATH . 'templates/shop-product-card.php';
            endwhile;
            echo '</div>';

            // Pagination
            echo '<div class="bookshive-pagination">';
            echo paginate_links([
                'total' => $query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
                'prev_text' => '« ' . __('Previous', 'bookshive'),
                'next_text' => __('Next', 'bookshive') . ' »',
            ]);
            echo '</div>';
        } else {
            echo '<p>' . esc_html__('No books available right now. Check back soon!', 'bookshive') . '</p>';
        }

        wp_reset_postdata();
        echo '</div>';
        return ob_get_clean();
    }

    /**
     * Shortcode: [bookshive_author_store author_id=""]
     * Displays all books by a specific author (for indie dashboards).
     */
    public static function render_author_store($atts = []) {
        $atts = shortcode_atts([
            'author_id' => get_current_user_id(),
            'posts_per_page' => -1
        ], $atts);

        $author_id = intval($atts['author_id']);

        if (!$author_id) {
            return '<p>' . esc_html__('Invalid author ID.', 'bookshive') . '</p>';
        }

        ob_start();
        wp_enqueue_style('bookshive-shop');
        wp_enqueue_script('bookshive-shop');

        $args = [
            'post_type' => 'bookshive_shop_book',
            'author' => $author_id,
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['posts_per_page'])
        ];

        $query = new WP_Query($args);

        echo '<div class="bookshive-author-shop">';
        echo '<h2>' . sprintf(esc_html__('%s’s Store', 'bookshive'), esc_html(get_the_author_meta('display_name', $author_id))) . '</h2>';

        if ($query->have_posts()) {
            echo '<div class="bookshive-grid">';
            while ($query->have_posts()) : $query->the_post();
                include BOOKSHIVE_PATH . 'templates/shop-product-card.php';
            endwhile;
            echo '</div>';
        } else {
            echo '<p>' . esc_html__('No books listed by this author yet.', 'bookshive') . '</p>';
        }

        wp_reset_postdata();
        echo '</div>';
        return ob_get_clean();
    }
}

Bookshive_Shop_Shortcodes::init();
