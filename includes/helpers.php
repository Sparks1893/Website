<?php
if (!defined('ABSPATH')) exit;

/**
 * Shared Helper Functions
 * Author: E. Durant
 */

class Bookshive_Helpers {

    /**
     * Get formatted date
     */
    public static function format_date($date) {
        if (!$date) return '';
        return date_i18n(get_option('date_format'), strtotime($date));
    }

    /**
     * Fetch book thumbnail with fallback
     */
    public static function get_thumbnail($book_id) {
        $thumb = get_the_post_thumbnail_url($book_id, 'medium');
        return $thumb ?: BOOKSHIVE_URL . 'assets/img/shop-placeholder.png';
    }

    /**
     * Get user display name or fallback
     */
    public static function get_user_name($user_id) {
        $user = get_userdata($user_id);
        return $user ? $user->display_name : __('Unknown Reader', 'bookshive');
    }

    /**
     * Sanitize and shorten text
     */
    public static function truncate($text, $limit = 100) {
        $text = wp_strip_all_tags($text);
        if (strlen($text) <= $limit) return $text;
        return substr($text, 0, $limit) . '…';
    }

    /**
     * Convert array to readable list
     */
    public static function readable_list($array) {
        if (!is_array($array) || empty($array)) return '';
        if (count($array) === 1) return $array[0];
        $last = array_pop($array);
        return implode(', ', $array) . ' & ' . $last;
    }

    /**
     * Generate affiliate link for retailer
     */
    public static function retailer_link($isbn, $retailer = 'amazon') {
        switch ($retailer) {
            case 'waterstones':
                return 'https://www.waterstones.com/books/search/term/' . urlencode($isbn);
            case 'google':
                return 'https://books.google.com/books?vid=ISBN' . urlencode($isbn);
            case 'kobo':
                return 'https://www.kobo.com/gb/en/search?query=' . urlencode($isbn);
            default:
                return 'https://www.amazon.co.uk/s?k=' . urlencode($isbn);
        }
    }
}
