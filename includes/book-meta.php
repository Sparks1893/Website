<?php
if (!defined('ABSPATH')) exit;

function plm_add_book_meta_boxes() {
    add_meta_box('plm_book_details', 'Book Details', 'plm_render_book_meta_box', 'plm_book', 'side', 'default');
}
add_action('add_meta_boxes', 'plm_add_book_meta_boxes');

function plm_render_book_meta_box($post) {
    $rating = get_post_meta($post->ID, 'plm_rating', true);
    $status = get_post_meta($post->ID, 'plm_status', true);
    $series = get_post_meta($post->ID, 'plm_series', true);

    echo '<label>Rating (1–5):</label>';
    echo '<input type="number" name="plm_rating" min="1" max="5" value="' . esc_attr($rating) . '" style="width:100%;">';

    echo '<label>Reading Status:</label>';
    echo '<select name="plm_status" style="width:100%">';
    echo '<option value="Unread" ' . selected($status, 'Unread', false) . '>Unread</option>';
    echo '<option value="Reading" ' . selected($status, 'Reading', false) . '>Reading</option>';
    echo '<option value="Completed" ' . selected($status, 'Completed', false) . '>Completed</option>';
    echo '</select>';

    echo '<label>Series Name:</label>';
    echo '<input type="text" name="plm_series" value="' . esc_attr($series) . '" style="width:100%;">';
}

function plm_save_book_meta($post_id) {
    if (array_key_exists('plm_rating', $_POST))
        update_post_meta($post_id, 'plm_rating', sanitize_text_field($_POST['plm_rating']));
    if (array_key_exists('plm_status', $_POST))
        update_post_meta($post_id, 'plm_status', sanitize_text_field($_POST['plm_status']));
    if (array_key_exists('plm_series', $_POST))
        update_post_meta($post_id, 'plm_series', sanitize_text_field($_POST['plm_series']));
}
add_action('save_post', 'plm_save_book_meta');
