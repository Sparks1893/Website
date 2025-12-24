<?php
if (!defined('ABSPATH')) exit;

/**
 * Template: Book Quick View Modal
 * Description: Displays book details in a modal popup (triggered by AJAX).
 * Author: E. Durant
 */
?>

<div id="bookshive-quickview-modal" class="bookshive-modal" style="display:none;">
    <div class="bookshive-modal-content">
        <span class="bookshive-close">&times;</span>
        
        <div class="bookshive-modal-body">
            <div class="book-cover">
                <img id="bookshive-qv-cover" src="" alt="<?php esc_attr_e('Book cover', 'bookshive'); ?>">
            </div>
            
            <div class="book-info">
                <h2 id="bookshive-qv-title"><?php esc_html_e('Loading...', 'bookshive'); ?></h2>
                <p id="bookshive-qv-author"></p>
                <p id="bookshive-qv-genre"></p>
                <p id="bookshive-qv-desc"></p>
                <p id="bookshive-qv-rating"></p>

                <div class="book-actions">
                    <a href="#" id="bookshive-qv-amazon" target="_blank" class="button book-link"><?php _e('View on Amazon', 'bookshive'); ?></a>
                    <a href="#" id="bookshive-qv-waterstones" target="_blank" class="button book-link"><?php _e('View on Waterstones', 'bookshive'); ?></a>
                    <a href="#" id="bookshive-qv-kobo" target="_blank" class="button book-link"><?php _e('View on Kobo', 'bookshive'); ?></a>
                </div>

                <div class="bookshive-status">
                    <label><?php _e('Reading Status:', 'bookshive'); ?></label>
                    <select id="bookshive-qv-status">
                        <option value="unread"><?php _e('Unread', 'bookshive'); ?></option>
                        <option value="reading"><?php _e('Currently Reading', 'bookshive'); ?></option>
                        <option value="paused"><?php _e('Paused', 'bookshive'); ?></option>
                        <option value="dnf"><?php _e('Did Not Finish', 'bookshive'); ?></option>
                        <option value="completed"><?php _e('Completed', 'bookshive'); ?></option>
                    </select>
                </div>

                <div class="bookshive-notes">
                    <label><?php _e('Notes:', 'bookshive'); ?></label>
                    <textarea id="bookshive-qv-notes" placeholder="<?php esc_attr_e('Add personal notes...', 'bookshive'); ?>"></textarea>
                    <button id="bookshive-save-notes" class="button-primary"><?php _e('Save Notes', 'bookshive'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
