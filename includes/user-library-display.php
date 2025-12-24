<?php
if (!defined('ABSPATH')) exit;

/**
 * Template: User Library Display
 * Description: Main library page with filters, layout controls, and book listings.
 */

?>

<div id="bookshive-library" class="bookshive-library-view" data-layout="grid">
    
    <?php if ($atts['show_filters'] === 'true'): ?>
        <div class="bookshive-filter-bar">
            <form id="bookshive-filter-form" class="bookshive-filters">
                <select name="genre" id="bookshive-filter-genre">
                    <option value=""><?php _e('All Genres', 'bookshive'); ?></option>
                    <option value="romance">Romance</option>
                    <option value="fantasy">Fantasy</option>
                    <option value="thriller">Thriller</option>
                    <option value="mystery">Mystery</option>
                    <option value="nonfiction">Non-Fiction</option>
                </select>

                <select name="rating" id="bookshive-filter-rating">
                    <option value=""><?php _e('Any Rating', 'bookshive'); ?></option>
                    <option value="5">★★★★★ & Up</option>
                    <option value="4">★★★★ & Up</option>
                    <option value="3">★★★ & Up</option>
                </select>

                <select name="spice" id="bookshive-filter-spice">
                    <option value=""><?php _e('Spice Level', 'bookshive'); ?></option>
                    <option value="1">🌶️</option>
                    <option value="2">🌶️🌶️</option>
                    <option value="3">🌶️🌶️🌶️</option>
                    <option value="4">🌶️🌶️🌶️🌶️</option>
                    <option value="5">🔥🔥🔥🔥🔥</option>
                </select>

                <input type="text" name="author" id="bookshive-filter-author" placeholder="<?php esc_attr_e('Search by author...', 'bookshive'); ?>" />

                <button type="submit" id="bookshive-filter-submit"><?php _e('Apply', 'bookshive'); ?></button>
            </form>

            <div class="bookshive-view-toggle">
                <button data-layout="grid" class="active" title="<?php esc_attr_e('Grid view', 'bookshive'); ?>">🔳</button>
                <button data-layout="list" title="<?php esc_attr_e('List view', 'bookshive'); ?>">📜</button>
                <button data-layout="shelf" title="<?php esc_attr_e('Shelf view', 'bookshive'); ?>">📚</button>
            </div>
        </div>
    <?php endif; ?>

    <div id="bookshive-books-container" class="bookshive-books-grid">
        <?php
        if (!empty($books)) {
            foreach ($books as $book) {
                include BOOKSHIVE_PATH . 'templates/partials/library-book-card.php';
            }
        } else {
            echo '<p class="bookshive-empty">' . esc_html__('Your library is empty. Add some books to get started!', 'bookshive') . '</p>';
        }
        ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('bookshive-filter-form');
    const container = document.getElementById('bookshive-books-container');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append('action', 'bookshive_filter_books');
            formData.append('nonce', bookshiveAjax.nonce);

            fetch(bookshiveAjax.ajax_url, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    container.innerHTML = data.data.html;
                } else {
                    container.innerHTML = `<p class="bookshive-empty">${data.data || 'No results.'}</p>`;
                }
            });
        });
    }

    // Layout toggle buttons
    document.querySelectorAll('.bookshive-view-toggle button').forEach(btn => {
        btn.addEventListener('click', e => {
            document.querySelectorAll('.bookshive-view-toggle button').forEach(b => b.classList.remove('active'));
            e.target.classList.add('active');
            const layout = e.target.dataset.layout;
            document.querySelector('#bookshive-library').dataset.layout = layout;
        });
    });
});
</script>
