<?php
if (!defined('ABSPATH')) exit;

function plm_library_shortcode() {
    $books = new WP_Query(array(
        'post_type' => 'plm_book',
        'posts_per_page' => -1
    ));

    ob_start();
    echo '<div class="plm-library-grid">';
    while ($books->have_posts()): $books->the_post();
        echo '<div class="plm-book-card">';
        if (has_post_thumbnail()) {
            the_post_thumbnail('medium');
        }
        echo '<h4>' . get_the_title() . '</h4>';
        echo '<p>Rating: ' . get_post_meta(get_the_ID(), 'plm_rating', true) . ' ⭐</p>';
        echo '</div>';
    endwhile;
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode('plm_library', 'plm_library_shortcode');

echo '<div class="plm-actions">
    <button class="plm-btn-wishlist" data-book="'.get_the_ID().'">♡ Wishlist</button>
    <button class="plm-btn-favorite" data-book="'.get_the_ID().'">❤️ Favorite</button>
    <button class="plm-btn-like" data-book="'.get_the_ID().'">👍 Like</button>
</div>';
