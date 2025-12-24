<?php
if (!defined('ABSPATH')) exit;

function plm_register_book_post_type() {
    $labels = array(
        'name' => 'Books',
        'singular_name' => 'Book',
        'menu_name' => 'Library',
        'add_new' => 'Add Book',
        'add_new_item' => 'Add New Book',
        'edit_item' => 'Edit Book',
        'all_items' => 'All Books',
        'view_item' => 'View Book'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-book-alt',
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
    );

    register_post_type('plm_book', $args);
}
add_action('init', 'plm_register_book_post_type');
