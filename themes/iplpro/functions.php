<?php
/**
 * IPL Pro functions and definitions
 *
 * @package iplpro
 */

// Define theme version
define('IPLPRO_VERSION', '1.0.0');

// Set up theme defaults and register support for various WordPress features
function iplpro_setup() {
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'iplpro'),
        'footer' => esc_html__('Footer Menu', 'iplpro'),
    ));

    // Switch default core markup to output valid HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Set up the WordPress core custom background feature
    add_theme_support('custom-background', array(
        'default-color' => 'f5f5f5',
    ));

    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');

    // Add support for editor styles
    add_theme_support('editor-styles');

    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'iplpro_setup');

// Set the content width in pixels
function iplpro_content_width() {
    $GLOBALS['content_width'] = apply_filters('iplpro_content_width', 1200);
}
add_action('after_setup_theme', 'iplpro_content_width', 0);

/**
 * Enqueue scripts and styles
 */
function iplpro_scripts() {
    // Enqueue Google Fonts - Poppins
    wp_enqueue_style('iplpro-google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);
    
    // Main stylesheet
    wp_enqueue_style('iplpro-style', get_stylesheet_uri(), array(), IPLPRO_VERSION);
    
    // Theme CSS
    wp_enqueue_style('iplpro-main', get_template_directory_uri() . '/assets/css/main.css', array(), IPLPRO_VERSION);
    
    // Responsive CSS
    wp_enqueue_style('iplpro-responsive', get_template_directory_uri() . '/assets/css/responsive.css', array(), IPLPRO_VERSION);
    
    // Main JavaScript
    wp_enqueue_script('iplpro-main', get_template_directory_uri() . '/assets/js/main.js', array(), IPLPRO_VERSION, true);
    
    // Stadium map JavaScript (only on seat selection page)
    if (is_page('select-seats')) {
        wp_enqueue_script('iplpro-stadium-map', get_template_directory_uri() . '/assets/js/stadium-map.js', array(), IPLPRO_VERSION, true);
    }
    
    // Booking JavaScript (only on booking pages)
    if (is_page(array('select-seats', 'booking-summary', 'payment'))) {
        wp_enqueue_script('iplpro-booking', get_template_directory_uri() . '/assets/js/booking.js', array(), IPLPRO_VERSION, true);
    }

    // Thread comments
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'iplpro_scripts');

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom Post Types.
 */
require get_template_directory() . '/inc/custom-post-types.php';

/**
 * ACF Fields configuration.
 */
require get_template_directory() . '/inc/acf-fields.php';

/**
 * QR Code and Payment Integration.
 */
require get_template_directory() . '/inc/payment-integration.php';

/**
 * Admin configurations.
 */
require get_template_directory() . '/inc/admin-settings.php';

/**
 * Create required pages on theme activation
 */
function iplpro_create_pages() {
    $pages = array(
        'matches' => array(
            'title' => 'IPL Matches',
            'content' => '<!-- wp:shortcode -->[matches_list]<!-- /wp:shortcode -->',
        ),
        'select-seats' => array(
            'title' => 'Select Seats',
            'content' => '',
        ),
        'booking-summary' => array(
            'title' => 'Booking Summary',
            'content' => '',
        ),
        'payment' => array(
            'title' => 'Payment',
            'content' => '',
        ),
        'order-confirmation' => array(
            'title' => 'Order Confirmation',
            'content' => '',
        ),
    );

    foreach ($pages as $slug => $page) {
        // Check if the page exists
        $page_exists = get_page_by_path($slug);

        // If the page doesn't exist, create it
        if (!$page_exists) {
            $page_id = wp_insert_post(array(
                'post_title' => $page['title'],
                'post_content' => $page['content'],
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_name' => $slug,
            ));
        }
    }
}
add_action('after_switch_theme', 'iplpro_create_pages');

/**
 * Debug helper function - only in development
 */
function iplpro_debug($data) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

/**
 * Fix for WP_Error handling in content-match.php
 * As per client requirements
 */
function iplpro_get_term_name($term_id, $fallback = 'Team Name') {
    $team = get_term($term_id);
    if (!is_wp_error($team) && !empty($team)) {
        return esc_html($team->name);
    } else {
        return $fallback;
    }
}

/**
 * Validate and sanitize UTR number
 */
function iplpro_validate_utr($utr) {
    // Sanitize the UTR
    $sanitized_utr = sanitize_text_field($utr);
    
    // Validate UTR pattern (alphanumeric, 12-22 characters)
    if (preg_match('/^[a-zA-Z0-9]{12,22}$/', $sanitized_utr)) {
        return $sanitized_utr;
    }
    
    return false;
}