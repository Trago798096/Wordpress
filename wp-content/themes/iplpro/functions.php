<?php
/**
 * IPL Pro theme functions and definitions
 * 
 * @package iplpro
 */

// Define theme constants
define('IPLPRO_VERSION', '1.0.0');
define('IPLPRO_DIR', get_template_directory());
define('IPLPRO_URI', get_template_directory_uri());

// Set up theme
function iplpro_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'iplpro'),
        'footer'  => esc_html__('Footer Menu', 'iplpro'),
    ));
}
add_action('after_setup_theme', 'iplpro_setup');

// Enqueue scripts and styles
function iplpro_scripts() {
    // Enqueue styles
    wp_enqueue_style('iplpro-main', IPLPRO_URI . '/assets/css/main.css', array(), IPLPRO_VERSION);
    wp_enqueue_style('iplpro-responsive', IPLPRO_URI . '/assets/css/responsive.css', array(), IPLPRO_VERSION);
    
    // Enqueue scripts
    wp_enqueue_script('jquery');
    wp_enqueue_script('iplpro-main', IPLPRO_URI . '/assets/js/main.js', array('jquery'), IPLPRO_VERSION, true);
    
    // Stadium map script only on seat selection page
    if (is_page_template('page-select-seats.php')) {
        wp_enqueue_script('iplpro-stadium-map', IPLPRO_URI . '/assets/js/stadium-map.js', array('jquery'), IPLPRO_VERSION, true);
    }
    
    // Booking script only on booking pages
    if (is_page_template('page-booking-summary.php') || is_page_template('page-payment.php')) {
        wp_enqueue_script('iplpro-booking', IPLPRO_URI . '/assets/js/booking.js', array('jquery'), IPLPRO_VERSION, true);
    }
    
    // Localize script with booking data
    wp_localize_script('iplpro-booking', 'iplpro_booking', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('iplpro_booking_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'iplpro_scripts');

// Admin styles and scripts
function iplpro_admin_scripts() {
    wp_enqueue_style('iplpro-admin', IPLPRO_URI . '/assets/css/admin.css', array(), IPLPRO_VERSION);
    wp_enqueue_script('iplpro-admin', IPLPRO_URI . '/assets/js/admin.js', array('jquery'), IPLPRO_VERSION, true);
}
add_action('admin_enqueue_scripts', 'iplpro_admin_scripts');

// Include required files
require_once IPLPRO_DIR . '/inc/custom-post-types.php';
require_once IPLPRO_DIR . '/inc/acf-fields.php';
require_once IPLPRO_DIR . '/inc/admin-settings.php';
require_once IPLPRO_DIR . '/inc/razorpay-integration.php';

// Utility function to check if a term exists and is not a WP_Error
function iplpro_get_valid_term($term_id, $taxonomy = 'team') {
    if (empty($term_id)) {
        return false;
    }
    
    $term = get_term($term_id, $taxonomy);
    
    if (is_wp_error($term) || !$term) {
        // Log the error for debugging
        if (is_wp_error($term)) {
            error_log('IPL Pro: Term error - ' . $term->get_error_message());
        }
        return false;
    }
    
    return $term;
}

// Utility function to get match details with proper error handling
function iplpro_get_match_details($match_id) {
    if (empty($match_id)) {
        return false;
    }
    
    $match = get_post($match_id);
    
    if (!$match || $match->post_type !== 'match') {
        return false;
    }
    
    // Get match meta data with ACF
    $match_date = get_field('match_date', $match_id);
    $stadium_id = get_field('stadium', $match_id);
    $team1_id = get_field('team_1', $match_id);
    $team2_id = get_field('team_2', $match_id);
    
    // Get term objects with error handling
    $stadium = iplpro_get_valid_term($stadium_id, 'stadium');
    $team1 = iplpro_get_valid_term($team1_id);
    $team2 = iplpro_get_valid_term($team2_id);
    
    // Get seat categories
    $seat_categories = get_field('seat_categories', $match_id);
    
    if (!$match_date || !$stadium || !$team1 || !$team2 || !$seat_categories) {
        return false;
    }
    
    return array(
        'id' => $match_id,
        'title' => $match->post_title,
        'date' => $match_date,
        'stadium' => $stadium,
        'team1' => $team1,
        'team2' => $team2,
        'seat_categories' => $seat_categories,
    );
}

// Calculate total with GST and service fee
function iplpro_calculate_total($ticket_price, $qty = 1) {
    // Base amount
    $base_amount = $ticket_price * $qty;
    
    // GST (18%)
    $gst_amount = $base_amount * 0.18;
    
    // Service fee (fixed ₹75)
    $service_fee = 75;
    
    // Total
    $total = $base_amount + $gst_amount + $service_fee;
    
    return array(
        'base_amount' => $base_amount,
        'gst_amount' => $gst_amount,
        'service_fee' => $service_fee,
        'total' => $total,
    );
}

// Format price in Indian Rupees
function iplpro_format_price($price) {
    return '₹' . number_format($price, 2);
}

// AJAX handlers for booking process
function iplpro_ajax_update_booking() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iplpro_booking_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
    }
    
    // Get booking data
    $match_id = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
    $seat_type = isset($_POST['seat_type']) ? sanitize_text_field($_POST['seat_type']) : '';
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 1;
    
    // Validate data
    if (!$match_id || !$seat_type) {
        wp_send_json_error(array('message' => 'Invalid booking data'));
    }
    
    // Get match details
    $match = iplpro_get_match_details($match_id);
    
    if (!$match) {
        wp_send_json_error(array('message' => 'Match not found'));
    }
    
    // Find selected seat category
    $selected_category = null;
    foreach ($match['seat_categories'] as $category) {
        if ($category['type'] === $seat_type) {
            $selected_category = $category;
            break;
        }
    }
    
    if (!$selected_category) {
        wp_send_json_error(array('message' => 'Seat category not found'));
    }
    
    // Check seat availability
    if ($selected_category['seats_available'] < $qty) {
        wp_send_json_error(array('message' => 'Not enough seats available'));
    }
    
    // Calculate totals
    $price_details = iplpro_calculate_total($selected_category['price'], $qty);
    
    // Store booking in session
    $_SESSION['iplpro_booking'] = array(
        'match_id' => $match_id,
        'match' => $match,
        'seat_type' => $seat_type,
        'seat_category' => $selected_category,
        'qty' => $qty,
        'price_details' => $price_details,
    );
    
    wp_send_json_success(array(
        'message' => 'Booking updated',
        'booking' => $_SESSION['iplpro_booking'],
    ));
}
add_action('wp_ajax_iplpro_update_booking', 'iplpro_ajax_update_booking');
add_action('wp_ajax_nopriv_iplpro_update_booking', 'iplpro_ajax_update_booking');

// Start session for booking management
function iplpro_start_session() {
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'iplpro_start_session');
