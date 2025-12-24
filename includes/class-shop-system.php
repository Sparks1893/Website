<?php
if (!defined('ABSPATH')) exit;

/**
 * Class: Bookshive_Shop_System
 * Description: Core shop backend for indie author book listings.
 *              Handles database setup, product registration, and admin menus.
 */

class Bookshive_Shop_System {

    public static function init() {
        add_action('init', [__CLASS__, 'register_post_type']);
        add_action('admin_menu', [__CLASS__, 'register_shop_menu']);
        add_action('add_meta_boxes', [__CLASS__, 'add_book_meta_box']);
        add_action('save_post', [__CLASS__, 'save_book_meta']);
    }

    /**
     * Register custom post type for Shop Books
     */
    public static function register_post_type() {
        $labels = [
            'name'               => __('Shop Books', 'bookshive'),
            'singular_name'      => __('Shop Book', 'bookshive'),
            'add_new'            => __('Add New Book', 'bookshive'),
            'add_new_item'       => __('Add New Book Listing', 'bookshive'),
            'edit_item'          => __('Edit Book', 'bookshive'),
            'new_item'           => __('New Book', 'bookshive'),
            'view_item'          => __('View Book', 'bookshive'),
            'search_items'       => __('Search Books', 'bookshive'),
            'not_found'          => __('No books found', 'bookshive'),
            'not_found_in_trash' => __('No books found in trash', 'bookshive'),
            'menu_name'          => __('Bookshop', 'bookshive'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'author', 'comments'],
            'has_archive'        => true,
            'rewrite'            => ['slug' => 'bookshop'],
            'show_in_rest'       => true,
            'menu_icon'          => 'dashicons-book-alt',
        ];

        register_post_type('bookshive_shop_book', $args);
    }

    /**
     * Add "Shop" menu in admin
     */
    public static function register_shop_menu() {
        add_submenu_page(
            'edit.php?post_type=bookshive_shop_book',
            __('Shop Settings', 'bookshive'),
            __('Settings', 'bookshive'),
            'manage_options',
            'bookshive-shop-settings',
            [__CLASS__, 'render_settings_page']
        );
    }

    /**
     * Render admin settings page
     */
    public static function render_settings_page() {
        ?>
        <div class="wrap bookshive-admin">
            <h1><?php _e('Bookshive Shop Settings', 'bookshive'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('bookshive_shop_settings');
                do_settings_sections('bookshive_shop_settings');
                submit_button(__('Save Settings', 'bookshive'));
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Add meta box for pricing, ISBN, and purchase links
     */
    public static function add_book_meta_box() {
        add_meta_box(
            'bookshive_book_meta',
            __('Book Details', 'bookshive'),
            [__CLASS__, 'render_book_meta_box'],
            'bookshive_shop_book',
            'normal',
            'high'
        );
    }

    /**
     * Render meta box content
     */
    public static function render_book_meta_box($post) {
        $price = get_post_meta($post->ID, '_bookshive_price', true);
        $isbn = get_post_meta($post->ID, '_bookshive_isbn', true);
        $buy_link = get_post_meta($post->ID, '_bookshive_buy_link', true);

        wp_nonce_field('bookshive_save_book_meta', 'bookshive_book_meta_nonce');
        ?>
        <p>
            <label for="bookshive_price"><strong><?php _e('Price (£)', 'bookshive'); ?></strong></label><br>
            <input type="text" id="bookshive_price" name="bookshive_price" value="<?php echo esc_attr($price); ?>" class="widefat">
        </p>
        <p>
            <label for="bookshive_isbn"><strong><?php _e('ISBN', 'bookshive'); ?></strong></label><br>
            <input type="text" id="bookshive_isbn" name="bookshive_isbn" value="<?php echo esc_attr($isbn); ?>" class="widefat">
        </p>
        <p>
            <label for="bookshive_buy_link"><strong><?php _e('External Purchase Link', 'bookshive'); ?></strong></label><br>
            <input type="url" id="bookshive_buy_link" name="bookshive_buy_link" value="<?php echo esc_attr($buy_link); ?>" class="widefat" placeholder="https://example.com/book-page">
        </p>
        <?php
    }

    /**
     * Save meta box data
     */
    public static function save_book_meta($post_id) {
        if (!isset($_POST['bookshive_book_meta_nonce']) || !wp_verify_nonce($_POST['bookshive_book_meta_nonce'], 'bookshive_save_book_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $fields = [
            '_bookshive_price' => sanitize_text_field($_POST['bookshive_price'] ?? ''),
            '_bookshive_isbn' => sanitize_text_field($_POST['bookshive_isbn'] ?? ''),
            '_bookshive_buy_link' => esc_url_raw($_POST['bookshive_buy_link'] ?? '')
        ];

        foreach ($fields as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }
}

Bookshive_Shop_System::init();
