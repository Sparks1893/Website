<?php
if (!defined('ABSPATH')) exit;

function plm_register_book_taxonomies() {
    // Genre Taxonomy
    register_taxonomy('plm_genre', 'plm_book', array(
        'label' => 'Genres',
        'rewrite' => array('slug' => 'genre'),
        'hierarchical' => true,
        'show_in_rest' => true,
    ));

    // Author Taxonomy
    register_taxonomy('plm_author', 'plm_book', array(
        'label' => 'Authors',
        'rewrite' => array('slug' => 'author'),
        'hierarchical' => false,
        'show_in_rest' => true,
    ));

    // Spice Level Taxonomy
    register_taxonomy('plm_spice', 'plm_book', array(
        'label' => 'Spice Level',
        'rewrite' => array('slug' => 'spice'),
        'hierarchical' => false,
        'show_in_rest' => true,
    ));
}
add_action('init', 'plm_register_book_taxonomies');
