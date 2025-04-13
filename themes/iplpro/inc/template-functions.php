<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function iplpro_pingback_header() {
    if (is_singular() && pings_open()) {
        printf('<link rel="pingback" href="%s">', esc_url(get_bloginfo('pingback_url')));
    }
}
add_action('wp_head', 'iplpro_pingback_header');

/**
 * Add custom classes to the body element
 *
 * @param array $classes CSS classes applied to the body element.
 * @return array Modified CSS classes
 */
function iplpro_body_classes($classes) {
    // Add class if no sidebar is present
    if (!is_active_sidebar('sidebar-1')) {
        $classes[] = 'no-sidebar';
    }
    
    // Add class for specific page templates
    if (is_page('select-seats')) {
        $classes[] = 'seat-selection-page';
    } elseif (is_page('booking-summary')) {
        $classes[] = 'booking-summary-page';
    } elseif (is_page('payment')) {
        $classes[] = 'payment-page';
    } elseif (is_page('order-confirmation')) {
        $classes[] = 'order-confirmation-page';
    }
    
    // Add class for single match pages
    if (is_singular('match')) {
        $classes[] = 'single-match-page';
    }
    
    return $classes;
}
add_filter('body_class', 'iplpro_body_classes');

/**
 * Add custom styles based on theme customizer settings
 */
function iplpro_customizer_styles() {
    $primary_color = get_theme_mod('iplpro_primary_color', '#ff4e00');
    $secondary_color = get_theme_mod('iplpro_secondary_color', '#1a2a47');
    
    $custom_css = "
        :root {
            --primary-color: {$primary_color};
            --secondary-color: {$secondary_color};
        }
    ";
    
    wp_add_inline_style('iplpro-style', $custom_css);
}
add_action('wp_enqueue_scripts', 'iplpro_customizer_styles', 11);

/**
 * Get a list of upcoming matches
 *
 * @param int $count Number of matches to retrieve
 * @return array Matches array
 */
function iplpro_get_upcoming_matches($count = 4) {
    $matches = new WP_Query(array(
        'post_type' => 'match',
        'posts_per_page' => $count,
        'meta_query' => array(
            array(
                'key' => 'match_status',
                'value' => 'upcoming',
                'compare' => '=',
            ),
        ),
        'meta_key' => 'match_date',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    ));
    
    return $matches;
}

/**
 * Format match date
 *
 * @param string $date_string Date string from ACF
 * @param bool $include_time Whether to include time
 * @return string Formatted date
 */
function iplpro_format_match_date($date_string, $include_time = true) {
    if (empty($date_string)) {
        return '';
    }
    
    $date = DateTime::createFromFormat('d/m/Y g:i a', $date_string);
    
    if (!$date) {
        return $date_string;
    }
    
    if ($include_time) {
        return $date->format('d M Y, g:i A');
    } else {
        return $date->format('d M Y');
    }
}

/**
 * Get remaining seats for a ticket type
 *
 * @param int $match_id Match ID
 * @param string $ticket_type Ticket type
 * @return int Number of available seats
 */
function iplpro_get_available_seats($match_id, $ticket_type) {
    $ticket_categories = get_field('ticket_categories', $match_id);
    
    if (!$ticket_categories || !is_array($ticket_categories)) {
        return 0;
    }
    
    foreach ($ticket_categories as $category) {
        if ($category['ticket_type'] === $ticket_type) {
            return intval($category['seats_available']);
        }
    }
    
    return 0;
}

/**
 * Get ticket price for a ticket type
 *
 * @param int $match_id Match ID
 * @param string $ticket_type Ticket type
 * @return float Ticket price
 */
function iplpro_get_ticket_price($match_id, $ticket_type) {
    $ticket_categories = get_field('ticket_categories', $match_id);
    
    if (!$ticket_categories || !is_array($ticket_categories)) {
        return 0;
    }
    
    foreach ($ticket_categories as $category) {
        if ($category['ticket_type'] === $ticket_type) {
            return floatval($category['ticket_price']);
        }
    }
    
    return 0;
}

/**
 * Calculate GST amount
 *
 * @param float $amount Base amount
 * @param float $percentage GST percentage
 * @return float GST amount
 */
function iplpro_calculate_gst($amount, $percentage = null) {
    if ($percentage === null) {
        $percentage = get_field('gst_percentage', 'option');
        
        if (empty($percentage)) {
            $percentage = 18; // Default GST percentage
        }
    }
    
    return ($amount * $percentage) / 100;
}

/**
 * Calculate service fee
 *
 * @param int $quantity Number of tickets
 * @return float Service fee
 */
function iplpro_calculate_service_fee($quantity = 1) {
    $fee = get_field('service_fee', 'option');
    
    if (empty($fee)) {
        $fee = 75; // Default service fee
    }
    
    return $fee * $quantity;
}

/**
 * Calculate total amount for tickets
 *
 * @param float $unit_price Ticket unit price
 * @param int $quantity Number of tickets
 * @return array Calculation breakdown
 */
function iplpro_calculate_total($unit_price, $quantity = 1) {
    $base_amount = $unit_price * $quantity;
    $service_fee = iplpro_calculate_service_fee($quantity);
    $gst_amount = iplpro_calculate_gst($base_amount);
    $total_amount = $base_amount + $service_fee + $gst_amount;
    
    return array(
        'unit_price' => $unit_price,
        'quantity' => $quantity,
        'base_amount' => $base_amount,
        'service_fee' => $service_fee,
        'gst_amount' => $gst_amount,
        'total_amount' => $total_amount,
    );
}

/**
 * Format currency
 *
 * @param float $amount Amount to format
 * @return string Formatted amount
 */
function iplpro_format_currency($amount) {
    return '₹' . number_format($amount, 2, '.', ',');
}

/**
 * Create a ticket order
 *
 * @param array $order_data Order data
 * @return int|WP_Error Order ID or error
 */
function iplpro_create_ticket_order($order_data) {
    // Validate required fields
    $required_fields = array('customer_name', 'customer_email', 'customer_phone', 'match_id', 'ticket_type', 'quantity', 'unit_price');
    
    foreach ($required_fields as $field) {
        if (empty($order_data[$field])) {
            return new WP_Error('missing_field', sprintf(__('Missing required field: %s', 'iplpro'), $field));
        }
    }
    
    // Generate order title
    $match = get_post($order_data['match_id']);
    $order_title = sprintf('Order for %s - %s', $order_data['customer_name'], $match->post_title);
    
    // Calculate amounts
    $calculations = iplpro_calculate_total($order_data['unit_price'], $order_data['quantity']);
    
    // Create order post
    $order_id = wp_insert_post(array(
        'post_title' => $order_title,
        'post_status' => 'publish',
        'post_type' => 'ticket_order',
    ));
    
    if (is_wp_error($order_id)) {
        return $order_id;
    }
    
    // Add order meta
    update_post_meta($order_id, '_customer_name', sanitize_text_field($order_data['customer_name']));
    update_post_meta($order_id, '_customer_email', sanitize_email($order_data['customer_email']));
    update_post_meta($order_id, '_customer_phone', sanitize_text_field($order_data['customer_phone']));
    update_post_meta($order_id, '_match_id', intval($order_data['match_id']));
    update_post_meta($order_id, '_ticket_type', sanitize_text_field($order_data['ticket_type']));
    update_post_meta($order_id, '_quantity', intval($order_data['quantity']));
    update_post_meta($order_id, '_unit_price', floatval($order_data['unit_price']));
    update_post_meta($order_id, '_base_amount', $calculations['base_amount']);
    update_post_meta($order_id, '_service_fee', $calculations['service_fee']);
    update_post_meta($order_id, '_gst_amount', $calculations['gst_amount']);
    update_post_meta($order_id, '_total_amount', $calculations['total_amount']);
    
    // Set payment method if provided
    if (!empty($order_data['payment_method'])) {
        update_post_meta($order_id, '_payment_method', sanitize_text_field($order_data['payment_method']));
    }
    
    return $order_id;
}

/**
 * Process booking form submission
 */
function iplpro_process_booking_form() {
    if (!isset($_POST['iplpro_booking_submit']) || !isset($_POST['iplpro_booking_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['iplpro_booking_nonce'], 'iplpro_booking_form')) {
        wp_die(__('Security check failed', 'iplpro'), __('Error', 'iplpro'), array('response' => 403));
    }
    
    // Get form data
    $match_id = isset($_POST['match_id']) ? intval($_POST['match_id']) : 0;
    $ticket_type = isset($_POST['ticket_type']) ? sanitize_text_field($_POST['ticket_type']) : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $customer_name = isset($_POST['customer_name']) ? sanitize_text_field($_POST['customer_name']) : '';
    $customer_email = isset($_POST['customer_email']) ? sanitize_email($_POST['customer_email']) : '';
    $customer_phone = isset($_POST['customer_phone']) ? sanitize_text_field($_POST['customer_phone']) : '';
    
    // Validate form data
    $errors = array();
    
    if (empty($match_id)) {
        $errors[] = __('Invalid match selection.', 'iplpro');
    }
    
    if (empty($ticket_type)) {
        $errors[] = __('Please select a ticket type.', 'iplpro');
    }
    
    if ($quantity < 1) {
        $errors[] = __('Please select at least one ticket.', 'iplpro');
    }
    
    if (empty($customer_name)) {
        $errors[] = __('Please enter your name.', 'iplpro');
    }
    
    if (empty($customer_email) || !is_email($customer_email)) {
        $errors[] = __('Please enter a valid email address.', 'iplpro');
    }
    
    if (empty($customer_phone)) {
        $errors[] = __('Please enter your phone number.', 'iplpro');
    }
    
    // Check seat availability
    $available_seats = iplpro_get_available_seats($match_id, $ticket_type);
    
    if ($quantity > $available_seats) {
        $errors[] = sprintf(__('Sorry, only %d seats are available for this ticket type.', 'iplpro'), $available_seats);
    }
    
    // If there are errors, redirect back to the form
    if (!empty($errors)) {
        $error_query_args = array('booking_errors' => implode('|', $errors));
        wp_redirect(add_query_arg($error_query_args, wp_get_referer()));
        exit;
    }
    
    // Get ticket price
    $unit_price = iplpro_get_ticket_price($match_id, $ticket_type);
    
    // Create the order
    $order_data = array(
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $customer_phone,
        'match_id' => $match_id,
        'ticket_type' => $ticket_type,
        'quantity' => $quantity,
        'unit_price' => $unit_price,
    );
    
    $order_id = iplpro_create_ticket_order($order_data);
    
    if (is_wp_error($order_id)) {
        $error_query_args = array('booking_errors' => $order_id->get_error_message());
        wp_redirect(add_query_arg($error_query_args, wp_get_referer()));
        exit;
    }
    
    // Redirect to payment page
    wp_redirect(add_query_arg('order_id', $order_id, get_permalink(get_page_by_path('payment'))));
    exit;
}
add_action('template_redirect', 'iplpro_process_booking_form');

/**
 * Display booking form errors
 */
function iplpro_display_booking_errors() {
    if (isset($_GET['booking_errors'])) {
        $errors = explode('|', $_GET['booking_errors']);
        
        if (!empty($errors)) {
            echo '<div class="booking-errors">';
            foreach ($errors as $error) {
                echo '<p class="error-message">' . esc_html($error) . '</p>';
            }
            echo '</div>';
        }
    }
}

/**
 * Create shortcode to display upcoming matches
 */
function iplpro_matches_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 4,
    ), $atts, 'matches_list');
    
    $count = intval($atts['count']);
    $matches = iplpro_get_upcoming_matches($count);
    
    ob_start();
    
    if ($matches->have_posts()) {
        echo '<div class="upcoming-matches">';
        
        while ($matches->have_posts()) {
            $matches->the_post();
            
            $match_id = get_the_ID();
            $home_team_id = get_field('home_team', $match_id);
            $away_team_id = get_field('away_team', $match_id);
            $stadium_id = get_field('stadium', $match_id);
            $match_date = get_field('match_date', $match_id);
            
            $home_team = iplpro_get_term_name($home_team_id);
            $away_team = iplpro_get_term_name($away_team_id);
            $stadium = iplpro_get_term_name($stadium_id, 'Stadium');
            
            ?>
            <div class="match-card">
                <div class="match-date">
                    <span class="date-icon"><i class="dashicons dashicons-calendar-alt"></i></span>
                    <span class="date-text"><?php echo esc_html(iplpro_format_match_date($match_date, false)); ?></span>
                    <span class="time-text"><?php echo esc_html(date('g:i A', strtotime($match_date))); ?> IST</span>
                </div>
                
                <div class="match-teams">
                    <div class="team team-home">
                        <div class="team-logo">
                            <?php 
                            $team_logo = get_field('team_logo', 'team_' . $home_team_id);
                            if ($team_logo) {
                                echo '<img src="' . esc_url($team_logo) . '" alt="' . esc_attr($home_team) . '">';
                            } else {
                                echo '<div class="team-placeholder">' . esc_html(substr($home_team, 0, 3)) . '</div>';
                            }
                            ?>
                        </div>
                        <div class="team-name"><?php echo esc_html($home_team); ?></div>
                    </div>
                    
                    <div class="vs-badge">vs</div>
                    
                    <div class="team team-away">
                        <div class="team-logo">
                            <?php 
                            $team_logo = get_field('team_logo', 'team_' . $away_team_id);
                            if ($team_logo) {
                                echo '<img src="' . esc_url($team_logo) . '" alt="' . esc_attr($away_team) . '">';
                            } else {
                                echo '<div class="team-placeholder">' . esc_html(substr($away_team, 0, 3)) . '</div>';
                            }
                            ?>
                        </div>
                        <div class="team-name"><?php echo esc_html($away_team); ?></div>
                    </div>
                </div>
                
                <div class="match-venue">
                    <span class="venue-icon"><i class="dashicons dashicons-location"></i></span>
                    <span class="venue-text"><?php echo esc_html($stadium); ?></span>
                </div>
                
                <div class="match-actions">
                    <?php
                    // Check if tickets are available
                    $ticket_categories = get_field('ticket_categories', $match_id);
                    $tickets_available = false;
                    
                    if ($ticket_categories && is_array($ticket_categories)) {
                        foreach ($ticket_categories as $category) {
                            if ($category['seats_available'] > 0) {
                                $tickets_available = true;
                                break;
                            }
                        }
                    }
                    ?>
                    
                    <?php if ($tickets_available) : ?>
                        <span class="availability-label">
                            <i class="dashicons dashicons-yes"></i> Tickets Available!
                        </span>
                        <a href="<?php the_permalink(); ?>" class="book-ticket-btn">Book Tickets</a>
                    <?php else : ?>
                        <span class="availability-label sold-out">
                            <i class="dashicons dashicons-no"></i> Sold Out
                        </span>
                        <a href="<?php the_permalink(); ?>" class="view-match-btn">View Match</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        }
        
        echo '</div>';
        
        if ($matches->found_posts > $count) {
            echo '<div class="view-all-matches">';
            echo '<a href="' . esc_url(get_post_type_archive_link('match')) . '" class="view-all-btn">' . __('View All Matches', 'iplpro') . '</a>';
            echo '</div>';
        }
    } else {
        echo '<div class="no-matches">';
        echo '<p>' . __('No upcoming matches found.', 'iplpro') . '</p>';
        echo '</div>';
    }
    
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('matches_list', 'iplpro_matches_shortcode');