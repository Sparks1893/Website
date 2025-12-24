<?php
if (!defined('ABSPATH')) exit;

function plm_add_admin_menu() {
    add_menu_page(
        'Personal Library',
        'Library',
        'manage_options',
        'plm-library',
        'plm_library_dashboard',
        'dashicons-book-alt',
        6
    );
}
add_action('admin_menu', 'plm_add_admin_menu');

function plm_library_dashboard() {
    echo '<div class="wrap">';
    echo '<h1>Personal Library Manager</h1>';
    echo '<p>Welcome to your library dashboard. More features coming next...</p>';
    echo '</div>';
}
