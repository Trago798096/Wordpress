<?php
/**
 * IPL Pro functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package iplpro
 */

if (!defined('IPLPRO_VERSION')) {
    // Replace the version number of the theme on each release.
    define('IPLPRO_VERSION', '1.0.0');
}

if (!function_exists('iplpro_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     */
    function iplpro_setup() {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         */
        load_theme_textdomain('iplpro', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(
            array(
                'menu-1' => esc_html__('Primary', 'iplpro'),
                'footer-menu' => esc_html__('Footer Menu', 'iplpro'),
            )
        );

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
            )
        );

        // Set up the WordPress core custom background feature.
        add_theme_support(
            'custom-background',
            apply_filters(
                'iplpro_custom_background_args',
                array(
                    'default-color' => 'ffffff',
                    'default-image' => '',
                )
            )
        );

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');

        /**
         * Add support for core custom logo.
         *
         * @link https://codex.wordpress.org/Theme_Logo
         */
        add_theme_support(
            'custom-logo',
            array(
                'height'      => 250,
                'width'       => 250,
                'flex-width'  => true,
                'flex-height' => true,
            )
        );
    }
endif;
add_action('after_setup_theme', 'iplpro_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function iplpro_content_width() {
    $GLOBALS['content_width'] = apply_filters('iplpro_content_width', 1140);
}
add_action('after_setup_theme', 'iplpro_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function iplpro_widgets_init() {
    register_sidebar(
        array(
            'name'          => esc_html__('Sidebar', 'iplpro'),
            'id'            => 'sidebar-1',
            'description'   => esc_html__('Add widgets here.', 'iplpro'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h2 class="widget-title">',
            'after_title'   => '</h2>',
        )
    );
    
    register_sidebar(
        array(
            'name'          => esc_html__('Footer 1', 'iplpro'),
            'id'            => 'footer-1',
            'description'   => esc_html__('First footer widget area.', 'iplpro'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="footer-title">',
            'after_title'   => '</h3>',
        )
    );
    
    register_sidebar(
        array(
            'name'          => esc_html__('Footer 2', 'iplpro'),
            'id'            => 'footer-2',
            'description'   => esc_html__('Second footer widget area.', 'iplpro'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="footer-title">',
            'after_title'   => '</h3>',
        )
    );
    
    register_sidebar(
        array(
            'name'          => esc_html__('Footer 3', 'iplpro'),
            'id'            => 'footer-3',
            'description'   => esc_html__('Third footer widget area.', 'iplpro'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="footer-title">',
            'after_title'   => '</h3>',
        )
    );
    
    register_sidebar(
        array(
            'name'          => esc_html__('Footer 4', 'iplpro'),
            'id'            => 'footer-4',
            'description'   => esc_html__('Fourth footer widget area.', 'iplpro'),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="footer-title">',
            'after_title'   => '</h3>',
        )
    );
}
add_action('widgets_init', 'iplpro_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function iplpro_scripts() {
    wp_enqueue_style('iplpro-style', get_stylesheet_uri(), array(), IPLPRO_VERSION);
    wp_enqueue_style('iplpro-main', get_template_directory_uri() . '/assets/css/main.css', array(), IPLPRO_VERSION);
    wp_enqueue_style('iplpro-responsive', get_template_directory_uri() . '/assets/css/responsive.css', array(), IPLPRO_VERSION);
    wp_enqueue_style('dashicons');
    
    // Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap', array(), null);
    
    wp_enqueue_script('iplpro-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), IPLPRO_VERSION, true);
    wp_enqueue_script('iplpro-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), IPLPRO_VERSION, true);
    
    // Add stadium map script only on single match pages
    if (is_singular('match')) {
        wp_enqueue_script('iplpro-stadium-map', get_template_directory_uri() . '/assets/js/stadium-map.js', array('jquery'), IPLPRO_VERSION, true);
    }
    
    // Add booking script on match and booking pages
    if (is_singular('match') || is_page(array('booking-summary', 'payment', 'order-confirmation'))) {
        wp_enqueue_script('iplpro-booking', get_template_directory_uri() . '/assets/js/booking.js', array('jquery'), IPLPRO_VERSION, true);
    }

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'iplpro_scripts');

/**
 * Load required theme files
 */
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/custom-post-types.php';
require get_template_directory() . '/inc/shortcodes.php';
require get_template_directory() . '/inc/payment-integration.php';
require get_template_directory() . '/inc/demo-data.php';

// Load ACF fields if ACF plugin is active
if (class_exists('ACF')) {
    require get_template_directory() . '/inc/acf-fields.php';
}

/**
 * Helper functions
 */

/**
 * Get term name from ID
 */
function iplpro_get_term_name($term_id, $taxonomy = 'team') {
    if (empty($term_id)) {
        return '';
    }
    
    $term = get_term($term_id, $taxonomy);
    
    if (!is_wp_error($term) && !empty($term)) {
        return $term->name;
    }
    
    return '';
}

/**
 * Format match date for better readability
 */
function iplpro_format_match_date($date_string, $format = 'M j, Y') {
    if (empty($date_string)) {
        return '';
    }
    
    $date = DateTime::createFromFormat('d/m/Y g:i a', $date_string);
    
    if (!$date) {
        return $date_string;
    }
    
    return $date->format($format);
}

/**
 * Format time from match date string
 */
function iplpro_format_match_time($date_string, $format = 'g:i A') {
    if (empty($date_string)) {
        return '';
    }
    
    $date = DateTime::createFromFormat('d/m/Y g:i a', $date_string);
    
    if (!$date) {
        return '';
    }
    
    return $date->format($format);
}

/**
 * Check if a match date is in the past
 */
function iplpro_match_is_past($date_string) {
    if (empty($date_string)) {
        return false;
    }
    
    $match_date = DateTime::createFromFormat('d/m/Y g:i a', $date_string);
    $now = new DateTime();
    
    if (!$match_date) {
        return false;
    }
    
    return $match_date < $now;
}

/**
 * Generate a unique order ID
 */
function iplpro_generate_order_id() {
    $prefix = 'IPL';
    $date = date('Ymd');
    $random = strtoupper(substr(md5(mt_rand()), 0, 5));
    
    return $prefix . $date . $random;
}

/**
 * Get ticket types from match
 */
function iplpro_get_ticket_types($match_id) {
    if (empty($match_id)) {
        return array();
    }
    
    $ticket_types = get_field('ticket_types', $match_id);
    
    if (empty($ticket_types)) {
        // Default ticket types if none are set
        return array(
            array(
                'ticket_name' => 'General Admission',
                'ticket_price' => 1000,
                'ticket_description' => 'General seating area'
            ),
            array(
                'ticket_name' => 'Premium',
                'ticket_price' => 3000,
                'ticket_description' => 'Premium seating area with better views'
            ),
            array(
                'ticket_name' => 'VIP Box',
                'ticket_price' => 6000,
                'ticket_description' => 'Exclusive VIP box with premium amenities'
            ),
        );
    }
    
    return $ticket_types;
}

/**
 * Modify archive title to remove prefix
 */
function iplpro_archive_title($title) {
    if (is_category()) {
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        $title = single_tag_title('', false);
    } elseif (is_author()) {
        $title = get_the_author();
    } elseif (is_tax()) {
        $title = single_term_title('', false);
    } elseif (is_post_type_archive()) {
        $title = post_type_archive_title('', false);
    }
    
    return $title;
}
add_filter('get_the_archive_title', 'iplpro_archive_title');

/**
 * Configure email settings for order confirmations
 */
function iplpro_configure_emails() {
    // Add filter for email settings if needed
}
add_action('init', 'iplpro_configure_emails');

/**
 * Create database tables if needed
 */
function iplpro_create_database_tables() {
    global $wpdb;
    
    // Add custom tables creation code if needed
}
add_action('after_switch_theme', 'iplpro_create_database_tables');