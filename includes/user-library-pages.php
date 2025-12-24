<?php
if (!defined('ABSPATH')) exit;

function plm_user_wishlist_page() {
    if (!is_user_logged_in()) return 'Please log in to view your wishlist.';
    global $wpdb;
    $table = $wpdb->prefix . 'plm_user_actions';
    $user_id = get_current_user_id();

    $book_ids = $wpdb->get_col("SELECT book_id FROM $table WHERE user_id=$user_id AND action_type='wishlist'");
    if (!$book_ids) return '<p>Your wishlist is empty.</p>';

    $output = '<h2>Your Wishlist</h2><ul>';
    foreach ($book_ids as $id) {
        $output .= '<li><a href="'.get_permalink($id).'">'.get_the_title($id).'</a></li>';
    }
    $output .= '</ul>';
    return $output;
}
add_shortcode('plm_wishlist', 'plm_user_wishlist_page');
