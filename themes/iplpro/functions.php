<?php
/**
 * IPL Pro functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package iplpro
 */

if (!defined('IPLPRO_VERSION')) {
	// Replace the version number as needed
	define('IPLPRO_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function iplpro_setup() {
	/*
	 * Make theme available for translation.
	 */
	load_theme_textdomain('iplpro', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
	 * Let WordPress manage the document title.
	 */
	add_theme_support('title-tag');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 */
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in multiple locations
	register_nav_menus(
		array(
			'primary' => esc_html__('Primary Menu', 'iplpro'),
			'footer' => esc_html__('Footer Menu', 'iplpro'),
		)
	);

	/*
	 * Switch default core markup to output valid HTML5.
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
add_action('after_setup_theme', 'iplpro_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 */
function iplpro_content_width() {
	$GLOBALS['content_width'] = apply_filters('iplpro_content_width', 1200);
}
add_action('after_setup_theme', 'iplpro_content_width', 0);

/**
 * Register widget area.
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
}
add_action('widgets_init', 'iplpro_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function iplpro_scripts() {
    // Dashicons for frontend use
    wp_enqueue_style('dashicons');

	wp_enqueue_style('iplpro-style', get_stylesheet_uri(), array(), IPLPRO_VERSION);
	wp_enqueue_style('iplpro-main', get_template_directory_uri() . '/assets/css/main.css', array(), IPLPRO_VERSION);
	wp_enqueue_style('iplpro-responsive', get_template_directory_uri() . '/assets/css/responsive.css', array(), IPLPRO_VERSION);

	wp_enqueue_script('iplpro-main', get_template_directory_uri() . '/assets/js/main.js', array(), IPLPRO_VERSION, true);

	if (is_singular('match')) {
		wp_enqueue_script('iplpro-stadium-map', get_template_directory_uri() . '/assets/js/stadium-map.js', array(), IPLPRO_VERSION, true);
		wp_enqueue_script('iplpro-booking', get_template_directory_uri() . '/assets/js/booking.js', array(), IPLPRO_VERSION, true);
	}

	if (is_page('booking-summary') || is_page('payment')) {
		wp_enqueue_script('iplpro-booking', get_template_directory_uri() . '/assets/js/booking.js', array(), IPLPRO_VERSION, true);
	}

	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts', 'iplpro_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Custom Post Types for IPL Pro.
 */
require get_template_directory() . '/inc/custom-post-types.php';

/**
 * ACF fields for IPL Pro.
 */
require get_template_directory() . '/inc/acf-fields.php';

/**
 * Payment integration for IPL Pro.
 */
require get_template_directory() . '/inc/payment-integration.php';

/**
 * Shortcodes for IPL Pro.
 */
require get_template_directory() . '/inc/shortcodes.php';

/**
 * Helper function to get term name
 */
function iplpro_get_term_name($term_id, $taxonomy = 'team') {
	if (empty($term_id)) {
		return '';
	}
	
	$term = get_term($term_id, $taxonomy);
	
	if (is_wp_error($term) || empty($term)) {
		return '';
	}
	
	return $term->name;
}

/**
 * Display match details
 */
function iplpro_match_details($match_id) {
	$home_team_id = get_field('home_team', $match_id);
	$away_team_id = get_field('away_team', $match_id);
	$match_date = get_field('match_date', $match_id);
	$stadium_id = get_field('stadium', $match_id);
	
	$home_team = iplpro_get_term_name($home_team_id);
	$away_team = iplpro_get_term_name($away_team_id);
	$stadium = iplpro_get_term_name($stadium_id, 'stadium');
	
	$home_team_logo = get_field('team_logo', 'team_' . $home_team_id);
	$away_team_logo = get_field('team_logo', 'team_' . $away_team_id);
	
	// Format date
	$match_datetime = DateTime::createFromFormat('d/m/Y g:i a', $match_date);
	$formatted_date = $match_datetime ? $match_datetime->format('l, F j, Y') : '';
	$formatted_time = $match_datetime ? $match_datetime->format('g:i A') : '';
	
	?>
	<div class="match-details">
		<div class="match-teams">
			<div class="team team-home">
				<?php if ($home_team_logo) : ?>
					<img src="<?php echo esc_url($home_team_logo['url']); ?>" alt="<?php echo esc_attr($home_team); ?>" class="team-logo">
				<?php else : ?>
					<div class="team-placeholder"><?php echo esc_html(substr($home_team, 0, 2)); ?></div>
				<?php endif; ?>
				<div class="team-name"><?php echo esc_html($home_team); ?></div>
			</div>
			
			<div class="vs-container">
				<span class="vs-text">VS</span>
			</div>
			
			<div class="team team-away">
				<?php if ($away_team_logo) : ?>
					<img src="<?php echo esc_url($away_team_logo['url']); ?>" alt="<?php echo esc_attr($away_team); ?>" class="team-logo">
				<?php else : ?>
					<div class="team-placeholder"><?php echo esc_html(substr($away_team, 0, 2)); ?></div>
				<?php endif; ?>
				<div class="team-name"><?php echo esc_html($away_team); ?></div>
			</div>
		</div>
		
		<div class="match-meta">
			<div class="match-meta-item">
				<span class="dashicons dashicons-calendar-alt"></span>
				<span class="meta-text"><?php echo esc_html($formatted_date); ?></span>
			</div>
			<div class="match-meta-item">
				<span class="dashicons dashicons-clock"></span>
				<span class="meta-text"><?php echo esc_html($formatted_time); ?></span>
			</div>
			<div class="match-meta-item">
				<span class="dashicons dashicons-location"></span>
				<span class="meta-text"><?php echo esc_html($stadium); ?></span>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Display stadium map
 */
function iplpro_stadium_map() {
	?>
	<div class="stadium-map-container">
		<h3 class="stadium-map-title"><?php esc_html_e('Select Your Section in the Stadium', 'iplpro'); ?></h3>
		
		<div class="stadium-map">
			<?php include get_template_directory() . '/assets/svg/stadium-map.svg'; ?>
			
			<div class="zoom-controls">
				<button class="zoom-btn zoom-in" title="<?php esc_attr_e('Zoom In', 'iplpro'); ?>">+</button>
				<button class="zoom-btn zoom-out" title="<?php esc_attr_e('Zoom Out', 'iplpro'); ?>">-</button>
				<button class="zoom-btn zoom-reset" title="<?php esc_attr_e('Reset Zoom', 'iplpro'); ?>">
					<span class="dashicons dashicons-image-rotate"></span>
				</button>
			</div>
		</div>
		
		<div class="selection-message" style="display: none;"></div>
	</div>
	<?php
}

/**
 * Display ticket categories
 */
function iplpro_ticket_categories($match_id) {
	$ticket_types = get_field('ticket_types', $match_id);
	
	if (empty($ticket_types)) {
		// Default ticket types if none are set
		$ticket_types = array(
			array(
				'ticket_name' => 'Premium',
				'ticket_price' => '7999',
				'ticket_description' => 'Best seats with perfect view of the action'
			),
			array(
				'ticket_name' => 'Corporate Box',
				'ticket_price' => '4999',
				'ticket_description' => 'Business seating with excellent amenities'
			),
			array(
				'ticket_name' => 'East Stand',
				'ticket_price' => '2999',
				'ticket_description' => 'Great side view of the match'
			),
			array(
				'ticket_name' => 'West Stand',
				'ticket_price' => '2999',
				'ticket_description' => 'Great side view of the match'
			),
			array(
				'ticket_name' => 'North Stand',
				'ticket_price' => '1499',
				'ticket_description' => 'Behind the north wicket'
			),
			array(
				'ticket_name' => 'South Stand',
				'ticket_price' => '1499',
				'ticket_description' => 'Behind the south wicket'
			)
		);
	}
	?>
	<div class="ticket-categories">
		<h3 class="ticket-categories-title"><?php esc_html_e('Select Ticket Type', 'iplpro'); ?></h3>
		
		<div class="ticket-selection">
			<?php foreach ($ticket_types as $ticket) : ?>
				<div class="ticket-type-card" data-ticket-type="<?php echo esc_attr($ticket['ticket_name']); ?>" data-price="<?php echo esc_attr($ticket['ticket_price']); ?>">
					<div class="ticket-type-header">
						<h4 class="ticket-type-name"><?php echo esc_html($ticket['ticket_name']); ?></h4>
						<div class="ticket-type-price">₹<?php echo number_format($ticket['ticket_price']); ?></div>
					</div>
					<div class="ticket-type-description">
						<?php echo esc_html($ticket['ticket_description']); ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Display booking form
 */
function iplpro_booking_form($match_id) {
	$match_title = get_the_title($match_id);
	$match_date = get_field('match_date', $match_id);
	
	// Generate unique order ID
	$order_id = 'IPL' . strtoupper(substr(md5(time() . rand(10, 99)), 0, 8));
	?>
	<div class="booking-form-container">
		<h3 class="booking-form-title"><?php esc_html_e('Booking Details', 'iplpro'); ?></h3>
		
		<form action="<?php echo esc_url(home_url('/booking-summary/')); ?>" method="post" class="booking-form">
			<input type="hidden" name="match_id" value="<?php echo esc_attr($match_id); ?>">
			<input type="hidden" name="match_title" value="<?php echo esc_attr($match_title); ?>">
			<input type="hidden" name="match_date" value="<?php echo esc_attr($match_date); ?>">
			<input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
			<input type="hidden" name="ticket_type" value="">
			
			<div class="ticket-summary">
				<div class="summary-row">
					<span class="summary-label"><?php esc_html_e('Selected Ticket:', 'iplpro'); ?></span>
					<span class="selected-ticket-type"></span>
				</div>
				
				<div class="summary-row">
					<span class="summary-label"><?php esc_html_e('Price per Ticket:', 'iplpro'); ?></span>
					<span class="ticket-price">₹0</span>
				</div>
				
				<div class="summary-row">
					<span class="summary-label"><?php esc_html_e('Quantity:', 'iplpro'); ?></span>
					<div class="quantity-control">
						<button type="button" class="qty-btn minus">-</button>
						<input type="number" name="quantity" value="1" min="1" max="10">
						<button type="button" class="qty-btn plus">+</button>
					</div>
				</div>
				
				<div class="summary-row total-row">
					<span class="summary-label"><?php esc_html_e('Total Amount:', 'iplpro'); ?></span>
					<span class="total-amount">₹0</span>
				</div>
			</div>
			
			<div class="customer-details">
				<h4><?php esc_html_e('Customer Information', 'iplpro'); ?></h4>
				
				<div class="form-row">
					<div class="form-field">
						<label for="customer_name"><?php esc_html_e('Full Name', 'iplpro'); ?></label>
						<input type="text" name="customer_name" id="customer_name" required>
					</div>
				</div>
				
				<div class="form-row two-columns">
					<div class="form-field">
						<label for="customer_email"><?php esc_html_e('Email', 'iplpro'); ?></label>
						<input type="email" name="customer_email" id="customer_email" required>
					</div>
					
					<div class="form-field">
						<label for="customer_phone"><?php esc_html_e('Phone', 'iplpro'); ?></label>
						<input type="tel" name="customer_phone" id="customer_phone" required>
					</div>
				</div>
				
				<div class="form-row">
					<div class="form-field">
						<label for="customer_address"><?php esc_html_e('Address', 'iplpro'); ?></label>
						<input type="text" name="customer_address" id="customer_address" required>
					</div>
				</div>
				
				<div class="form-row two-columns">
					<div class="form-field">
						<label for="customer_city"><?php esc_html_e('City', 'iplpro'); ?></label>
						<input type="text" name="customer_city" id="customer_city" required>
					</div>
					
					<div class="form-field">
						<label for="customer_pincode"><?php esc_html_e('Pincode', 'iplpro'); ?></label>
						<input type="text" name="customer_pincode" id="customer_pincode" required>
					</div>
				</div>
			</div>
			
			<div class="booking-form-footer">
				<p class="booking-terms">
					<?php esc_html_e('By proceeding, you agree to our Terms & Conditions and Refund Policy', 'iplpro'); ?>
				</p>
				
				<button type="submit" class="booking-submit-btn"><?php esc_html_e('Proceed to Payment', 'iplpro'); ?></button>
			</div>
		</form>
	</div>
	<?php
}

/**
 * Add rewrite rules for booking pages
 */
function iplpro_add_rewrite_rules() {
    add_rewrite_rule(
        '^booking-summary/?$',
        'index.php?pagename=booking-summary',
        'top'
    );
    
    add_rewrite_rule(
        '^payment/?$',
        'index.php?pagename=payment',
        'top'
    );
    
    add_rewrite_rule(
        '^order-confirmation/?$',
        'index.php?pagename=order-confirmation',
        'top'
    );
}
add_action('init', 'iplpro_add_rewrite_rules');

/**
 * Add admin notice if ACF is not active
 */
function iplpro_admin_notice_acf_not_active() {
    if (!is_plugin_active('advanced-custom-fields/acf.php') && !is_plugin_active('advanced-custom-fields-pro/acf.php')) {
        ?>
        <div class="notice notice-error">
            <p><?php _e('The IPL Pro theme requires the Advanced Custom Fields plugin to be installed and activated.', 'iplpro'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'iplpro_admin_notice_acf_not_active');