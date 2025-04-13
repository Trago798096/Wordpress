<?php
/**
 * Razorpay Integration for IPL Pro theme
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize Razorpay integration
 */
function iplpro_razorpay_init() {
    // Start session if not started
    if (!session_id()) {
        session_start();
    }
}
add_action('init', 'iplpro_razorpay_init');

/**
 * Get Razorpay API keys
 * First tries environment variables, then falls back to ACF options
 */
function iplpro_get_razorpay_keys() {
    // Try to get from environment variables first
    $key_id = getenv('RAZORPAY_KEY_ID');
    $key_secret = getenv('RAZORPAY_KEY_SECRET');
    
    // Fall back to ACF options if not found in environment
    if (!$key_id || !$key_secret) {
        $payment_settings = get_field('payment_settings', 'option');
        
        if ($payment_settings) {
            $key_id = $payment_settings['razorpay_key_id'] ?? '';
            $key_secret = $payment_settings['razorpay_key_secret'] ?? '';
        }
    }
    
    // Use test mode if enabled in settings
    $test_mode = false;
    $payment_settings = get_field('payment_settings', 'option');
    if ($payment_settings && isset($payment_settings['enable_test_mode'])) {
        $test_mode = (bool) $payment_settings['enable_test_mode'];
    }
    
    // If in test mode and no keys provided, use Razorpay test keys
    if ($test_mode && (empty($key_id) || empty($key_secret))) {
        $key_id = 'rzp_test_1DP5mmOlF5G5ag';
        $key_secret = 'thisShouldBeReplaced';
    }
    
    return array(
        'key_id' => $key_id,
        'key_secret' => $key_secret,
        'test_mode' => $test_mode,
    );
}

/**
 * Create Razorpay order
 * 
 * @param array $booking_data The booking data
 * @return array|WP_Error Order data or error
 */
function iplpro_create_razorpay_order($booking_data) {
    if (empty($booking_data) || !isset($booking_data['total_amount'])) {
        return new WP_Error('invalid_booking', __('Invalid booking data', 'iplpro'));
    }
    
    // Get Razorpay keys
    $razorpay_keys = iplpro_get_razorpay_keys();
    $key_id = $razorpay_keys['key_id'];
    $key_secret = $razorpay_keys['key_secret'];
    
    if (empty($key_id) || empty($key_secret)) {
        return new WP_Error('missing_keys', __('Razorpay API keys are not configured', 'iplpro'));
    }
    
    // Prepare order data
    $order_data = array(
        'amount' => $booking_data['total_amount'] * 100, // Amount in paise
        'currency' => 'INR',
        'receipt' => 'rcpt_' . time(),
        'payment_capture' => 1, // Auto-capture
        'notes' => array(
            'match_id' => $booking_data['match_id'] ?? '',
            'seat_type' => $booking_data['seat_type'] ?? '',
            'quantity' => $booking_data['quantity'] ?? 1,
            'customer_name' => $booking_data['customer_name'] ?? '',
            'customer_email' => $booking_data['customer_email'] ?? '',
            'customer_phone' => $booking_data['customer_phone'] ?? '',
        ),
    );
    
    // Make API request to create order
    $auth = base64_encode($key_id . ':' . $key_secret);
    
    $args = array(
        'headers' => array(
            'Authorization' => 'Basic ' . $auth,
            'Content-Type' => 'application/json',
        ),
        'body' => json_encode($order_data),
        'timeout' => 30,
    );
    
    $response = wp_remote_post('https://api.razorpay.com/v1/orders', $args);
    
    // Check for errors
    if (is_wp_error($response)) {
        return $response;
    }
    
    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);
    
    if (isset($result['error'])) {
        return new WP_Error(
            'razorpay_error',
            $result['error']['description'] ?? __('Unknown Razorpay error', 'iplpro')
        );
    }
    
    return $result;
}

/**
 * Verify Razorpay payment
 * 
 * @param array $payment_data The payment data
 * @return bool|WP_Error True if verified, error otherwise
 */
function iplpro_verify_razorpay_payment($payment_data) {
    if (empty($payment_data) || 
        !isset($payment_data['razorpay_order_id']) || 
        !isset($payment_data['razorpay_payment_id']) || 
        !isset($payment_data['razorpay_signature'])) {
        return new WP_Error('invalid_payment', __('Invalid payment data', 'iplpro'));
    }
    
    // Get Razorpay keys
    $razorpay_keys = iplpro_get_razorpay_keys();
    $key_secret = $razorpay_keys['key_secret'];
    
    if (empty($key_secret)) {
        return new WP_Error('missing_keys', __('Razorpay API keys are not configured', 'iplpro'));
    }
    
    // Verify signature
    $expected_signature = hash_hmac(
        'sha256', 
        $payment_data['razorpay_order_id'] . '|' . $payment_data['razorpay_payment_id'], 
        $key_secret
    );
    
    if ($expected_signature !== $payment_data['razorpay_signature']) {
        return new WP_Error('invalid_signature', __('Payment signature verification failed', 'iplpro'));
    }
    
    return true;
}

/**
 * Process ticket booking and payment
 * 
 * @param array $booking_data The booking data
 * @return array|WP_Error Result of the booking process
 */
function iplpro_process_booking($booking_data) {
    if (empty($booking_data)) {
        return new WP_Error('invalid_booking', __('Invalid booking data', 'iplpro'));
    }
    
    // Validate required fields
    $required_fields = array('match_id', 'seat_type', 'quantity', 'customer_name', 'customer_email', 'customer_phone');
    foreach ($required_fields as $field) {
        if (!isset($booking_data[$field]) || empty($booking_data[$field])) {
            return new WP_Error('missing_field', sprintf(__('Missing required field: %s', 'iplpro'), $field));
        }
    }
    
    // Get match details
    $match = iplpro_get_match_details($booking_data['match_id']);
    if (!$match) {
        return new WP_Error('match_not_found', __('Match not found', 'iplpro'));
    }
    
    // Find selected seat category
    $selected_category = null;
    foreach ($match['seat_categories'] as $category) {
        if ($category['type'] === $booking_data['seat_type']) {
            $selected_category = $category;
            break;
        }
    }
    
    if (!$selected_category) {
        return new WP_Error('seat_type_not_found', __('Seat category not found', 'iplpro'));
    }
    
    // Check seat availability
    if ($selected_category['seats_available'] < $booking_data['quantity']) {
        return new WP_Error('insufficient_seats', __('Not enough seats available', 'iplpro'));
    }
    
    // Calculate pricing
    $price_details = iplpro_calculate_total($selected_category['price'], $booking_data['quantity']);
    $booking_data['total_amount'] = $price_details['total'];
    
    // Create Razorpay order
    $order = iplpro_create_razorpay_order($booking_data);
    if (is_wp_error($order)) {
        return $order;
    }
    
    // Update booking data with order details
    $booking_data['razorpay_order_id'] = $order['id'];
    $booking_data['razorpay_currency'] = $order['currency'];
    $booking_data['razorpay_amount'] = $order['amount'] / 100; // Convert from paise to rupees
    
    // Store booking data in session
    $_SESSION['iplpro_booking'] = $booking_data;
    
    return array(
        'success' => true,
        'booking' => $booking_data,
        'payment' => $order,
    );
}

/**
 * Handle Razorpay payment callback
 */
function iplpro_handle_payment_callback() {
    // Check if this is a Razorpay callback
    if (!isset($_GET['razorpay_payment_id']) || !isset($_GET['razorpay_order_id']) || !isset($_GET['razorpay_signature'])) {
        return;
    }
    
    // Get payment data
    $payment_data = array(
        'razorpay_payment_id' => sanitize_text_field($_GET['razorpay_payment_id']),
        'razorpay_order_id' => sanitize_text_field($_GET['razorpay_order_id']),
        'razorpay_signature' => sanitize_text_field($_GET['razorpay_signature']),
    );
    
    // Verify payment
    $verification = iplpro_verify_razorpay_payment($payment_data);
    
    if (is_wp_error($verification)) {
        // Log the error
        error_log('Razorpay payment verification failed: ' . $verification->get_error_message());
        
        // Redirect to error page
        wp_redirect(add_query_arg('payment_error', 'verification_failed', home_url('/booking-failed')));
        exit;
    }
    
    // Get booking data from session
    $booking_data = isset($_SESSION['iplpro_booking']) ? $_SESSION['iplpro_booking'] : array();
    
    if (empty($booking_data)) {
        wp_redirect(add_query_arg('payment_error', 'no_booking_data', home_url('/booking-failed')));
        exit;
    }
    
    // Update booking with payment details
    $booking_data['payment_id'] = $payment_data['razorpay_payment_id'];
    $booking_data['payment_status'] = 'completed';
    $booking_data['payment_date'] = current_time('mysql');
    
    // Update seat availability
    iplpro_update_seat_availability($booking_data['match_id'], $booking_data['seat_type'], $booking_data['quantity']);
    
    // Create booking record (custom post)
    $booking_id = iplpro_create_booking_record($booking_data);
    
    if (is_wp_error($booking_id)) {
        // Log the error
        error_log('Failed to create booking record: ' . $booking_id->get_error_message());
        
        // Redirect to confirmation page anyway, since payment was successful
        wp_redirect(add_query_arg('payment', 'success', home_url('/booking-confirmed')));
        exit;
    }
    
    // Store booking ID in session
    $_SESSION['iplpro_booking']['booking_id'] = $booking_id;
    
    // Send confirmation email
    iplpro_send_booking_confirmation($booking_data);
    
    // Redirect to confirmation page
    wp_redirect(add_query_arg(array(
        'payment' => 'success',
        'booking_id' => $booking_id,
    ), home_url('/booking-confirmed')));
    exit;
}
add_action('template_redirect', 'iplpro_handle_payment_callback');

/**
 * Update seat availability after successful booking
 * 
 * @param int $match_id Match ID
 * @param string $seat_type Seat category type
 * @param int $quantity Number of seats booked
 * @return bool Success status
 */
function iplpro_update_seat_availability($match_id, $seat_type, $quantity) {
    // Get current seat categories
    $seat_categories = get_field('seat_categories', $match_id);
    
    if (empty($seat_categories)) {
        return false;
    }
    
    // Find and update the selected category
    foreach ($seat_categories as $key => $category) {
        if ($category['type'] === $seat_type) {
            // Calculate new availability
            $new_availability = max(0, intval($category['seats_available']) - intval($quantity));
            $seat_categories[$key]['seats_available'] = $new_availability;
            
            // Update the field
            update_field('seat_categories', $seat_categories, $match_id);
            return true;
        }
    }
    
    return false;
}

/**
 * Create booking record as custom post
 * 
 * @param array $booking_data Booking data
 * @return int|WP_Error Booking ID or error
 */
function iplpro_create_booking_record($booking_data) {
    // Create a unique booking number
    $booking_number = 'IPL-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    
    // Get match details for the title
    $match = iplpro_get_match_details($booking_data['match_id']);
    if (!$match) {
        return new WP_Error('match_not_found', __('Match not found', 'iplpro'));
    }
    
    // Create post data
    $post_data = array(
        'post_title'    => $booking_number . ' - ' . $booking_data['customer_name'],
        'post_content'  => sprintf(
            __('Booking for %1$s vs %2$s at %3$s on %4$s', 'iplpro'),
            $match['team1']->name,
            $match['team2']->name,
            $match['stadium']->name,
            date('d M Y, g:i A', strtotime($match['date']))
        ),
        'post_status'   => 'publish',
        'post_type'     => 'booking',
        'meta_input'    => array(
            'booking_number'    => $booking_number,
            'match_id'          => $booking_data['match_id'],
            'seat_type'         => $booking_data['seat_type'],
            'quantity'          => $booking_data['quantity'],
            'total_amount'      => $booking_data['total_amount'],
            'customer_name'     => $booking_data['customer_name'],
            'customer_email'    => $booking_data['customer_email'],
            'customer_phone'    => $booking_data['customer_phone'],
            'payment_id'        => $booking_data['payment_id'],
            'payment_status'    => $booking_data['payment_status'],
            'payment_date'      => $booking_data['payment_date'],
        ),
    );
    
    // Insert the post
    $booking_id = wp_insert_post($post_data);
    
    if (is_wp_error($booking_id)) {
        return $booking_id;
    }
    
    return $booking_id;
}

/**
 * Send booking confirmation email
 * 
 * @param array $booking_data Booking data
 * @return bool Success status
 */
function iplpro_send_booking_confirmation($booking_data) {
    // Get match details
    $match = iplpro_get_match_details($booking_data['match_id']);
    if (!$match) {
        return false;
    }
    
    // Find selected seat category
    $selected_category = null;
    foreach ($match['seat_categories'] as $category) {
        if ($category['type'] === $booking_data['seat_type']) {
            $selected_category = $category;
            break;
        }
    }
    
    if (!$selected_category) {
        return false;
    }
    
    // Calculate pricing
    $price_details = iplpro_calculate_total($selected_category['price'], $booking_data['quantity']);
    
    // Format match date
    $date_obj = new DateTime($match['date']);
    $formatted_date = $date_obj->format('d F Y, g:i A T');
    
    // Prepare email content
    $to = $booking_data['customer_email'];
    $subject = sprintf(__('Your booking confirmation for %s vs %s', 'iplpro'), $match['team1']->name, $match['team2']->name);
    
    $message = '<html><body>';
    $message .= '<h1>' . __('Booking Confirmed!', 'iplpro') . '</h1>';
    $message .= '<p>' . __('Thank you for booking tickets with IPL Pro.', 'iplpro') . '</p>';
    
    $message .= '<h2>' . __('Booking Details', 'iplpro') . '</h2>';
    $message .= '<table style="border-collapse: collapse; width: 100%;">';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Booking Number', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $booking_data['booking_number'] . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Match', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $match['team1']->name . ' vs ' . $match['team2']->name . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Date & Time', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $formatted_date . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Venue', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $match['stadium']->name . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Ticket Type', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $booking_data['seat_type'] . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Quantity', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $booking_data['quantity'] . '</td></tr>';
    $message .= '</table>';
    
    $message .= '<h2>' . __('Payment Summary', 'iplpro') . '</h2>';
    $message .= '<table style="border-collapse: collapse; width: 100%;">';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Base Amount', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . iplpro_format_price($price_details['base_amount']) . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('GST (18%)', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . iplpro_format_price($price_details['gst_amount']) . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Service Fee', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . iplpro_format_price($price_details['service_fee']) . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Total Amount', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . iplpro_format_price($price_details['total']) . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Payment ID', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $booking_data['payment_id'] . '</td></tr>';
    $message .= '<tr><td style="padding: 8px; border: 1px solid #ddd;"><strong>' . __('Payment Status', 'iplpro') . ':</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . ucfirst($booking_data['payment_status']) . '</td></tr>';
    $message .= '</table>';
    
    $message .= '<p>' . __('Please bring a printed copy of this email or show it on your mobile device at the stadium entrance.', 'iplpro') . '</p>';
    $message .= '<p>' . __('Thank you for choosing IPL Pro!', 'iplpro') . '</p>';
    $message .= '</body></html>';
    
    // Set email headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: IPL Pro <noreply@' . $_SERVER['HTTP_HOST'] . '>',
    );
    
    // Send email
    return wp_mail($to, $subject, $message, $headers);
}

/**
 * Register AJAX handler for Razorpay payment creation
 */
function iplpro_ajax_create_razorpay_payment() {
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'iplpro_booking_nonce')) {
        wp_send_json_error(array('message' => __('Security check failed', 'iplpro')));
    }
    
    // Get booking data from POST
    $booking_data = array(
        'match_id' => isset($_POST['match_id']) ? intval($_POST['match_id']) : 0,
        'seat_type' => isset($_POST['seat_type']) ? sanitize_text_field($_POST['seat_type']) : '',
        'quantity' => isset($_POST['quantity']) ? intval($_POST['quantity']) : 1,
        'customer_name' => isset($_POST['fullname']) ? sanitize_text_field($_POST['fullname']) : '',
        'customer_email' => isset($_POST['email']) ? sanitize_email($_POST['email']) : '',
        'customer_phone' => isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '',
    );
    
    // Process booking
    $result = iplpro_process_booking($booking_data);
    
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }
    
    wp_send_json_success($result);
}
add_action('wp_ajax_iplpro_create_razorpay_payment', 'iplpro_ajax_create_razorpay_payment');
add_action('wp_ajax_nopriv_iplpro_create_razorpay_payment', 'iplpro_ajax_create_razorpay_payment');

/**
 * Register custom post type for bookings
 */
function iplpro_register_booking_post_type() {
    $labels = array(
        'name'                  => _x('Bookings', 'Post Type General Name', 'iplpro'),
        'singular_name'         => _x('Booking', 'Post Type Singular Name', 'iplpro'),
        'menu_name'             => __('Bookings', 'iplpro'),
        'name_admin_bar'        => __('Booking', 'iplpro'),
        'archives'              => __('Booking Archives', 'iplpro'),
        'attributes'            => __('Booking Attributes', 'iplpro'),
        'parent_item_colon'     => __('Parent Booking:', 'iplpro'),
        'all_items'             => __('All Bookings', 'iplpro'),
        'add_new_item'          => __('Add New Booking', 'iplpro'),
        'add_new'               => __('Add New', 'iplpro'),
        'new_item'              => __('New Booking', 'iplpro'),
        'edit_item'             => __('Edit Booking', 'iplpro'),
        'update_item'           => __('Update Booking', 'iplpro'),
        'view_item'             => __('View Booking', 'iplpro'),
        'view_items'            => __('View Bookings', 'iplpro'),
        'search_items'          => __('Search Booking', 'iplpro'),
        'not_found'             => __('Not found', 'iplpro'),
        'not_found_in_trash'    => __('Not found in Trash', 'iplpro'),
        'featured_image'        => __('Featured Image', 'iplpro'),
        'set_featured_image'    => __('Set featured image', 'iplpro'),
        'remove_featured_image' => __('Remove featured image', 'iplpro'),
        'use_featured_image'    => __('Use as featured image', 'iplpro'),
        'insert_into_item'      => __('Insert into booking', 'iplpro'),
        'uploaded_to_this_item' => __('Uploaded to this booking', 'iplpro'),
        'items_list'            => __('Bookings list', 'iplpro'),
        'items_list_navigation' => __('Bookings list navigation', 'iplpro'),
        'filter_items_list'     => __('Filter bookings list', 'iplpro'),
    );
    
    $args = array(
        'label'                 => __('Booking', 'iplpro'),
        'description'           => __('Ticket bookings for matches', 'iplpro'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'custom-fields'),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-tickets-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'post',
        'show_in_rest'          => false,
    );
    
    register_post_type('booking', $args);
}
add_action('init', 'iplpro_register_booking_post_type');

/**
 * Add custom columns to booking post type
 */
function iplpro_booking_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = $value;
            $new_columns['booking_number'] = __('Booking #', 'iplpro');
            $new_columns['match'] = __('Match', 'iplpro');
            $new_columns['customer'] = __('Customer', 'iplpro');
        } else if ($key === 'date') {
            $new_columns['tickets'] = __('Tickets', 'iplpro');
            $new_columns['amount'] = __('Amount', 'iplpro');
            $new_columns['payment_status'] = __('Payment Status', 'iplpro');
            $new_columns[$key] = $value;
        } else {
            $new_columns[$key] = $value;
        }
    }
    return $new_columns;
}
add_filter('manage_booking_posts_columns', 'iplpro_booking_columns');

/**
 * Add content to custom columns for booking post type
 */
function iplpro_booking_custom_column($column, $post_id) {
    switch ($column) {
        case 'booking_number':
            $booking_number = get_post_meta($post_id, 'booking_number', true);
            echo esc_html($booking_number);
            break;
            
        case 'match':
            $match_id = get_post_meta($post_id, 'match_id', true);
            $match = iplpro_get_match_details($match_id);
            
            if ($match) {
                echo esc_html($match['team1']->name . ' vs ' . $match['team2']->name);
                echo '<br><small>' . esc_html(date('d M Y, g:i A', strtotime($match['date']))) . '</small>';
            } else {
                echo '—';
            }
            break;
            
        case 'customer':
            $customer_name = get_post_meta($post_id, 'customer_name', true);
            $customer_email = get_post_meta($post_id, 'customer_email', true);
            $customer_phone = get_post_meta($post_id, 'customer_phone', true);
            
            echo esc_html($customer_name);
            if ($customer_email) {
                echo '<br><small>' . esc_html($customer_email) . '</small>';
            }
            if ($customer_phone) {
                echo '<br><small>' . esc_html($customer_phone) . '</small>';
            }
            break;
            
        case 'tickets':
            $quantity = get_post_meta($post_id, 'quantity', true);
            $seat_type = get_post_meta($post_id, 'seat_type', true);
            
            echo esc_html($quantity . ' × ' . $seat_type);
            break;
            
        case 'amount':
            $total_amount = get_post_meta($post_id, 'total_amount', true);
            echo iplpro_format_price($total_amount);
            break;
            
        case 'payment_status':
            $payment_status = get_post_meta($post_id, 'payment_status', true);
            $payment_id = get_post_meta($post_id, 'payment_id', true);
            
            echo '<span class="payment-status payment-status-' . esc_attr($payment_status) . '">' . esc_html(ucfirst($payment_status)) . '</span>';
            if ($payment_id) {
                echo '<br><small>' . esc_html($payment_id) . '</small>';
            }
            break;
    }
}
add_action('manage_booking_posts_custom_column', 'iplpro_booking_custom_column', 10, 2);

/**
 * Add styles for booking custom columns
 */
function iplpro_booking_columns_style() {
    ?>
    <style type="text/css">
        .post-type-booking .payment-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .post-type-booking .payment-status-completed {
            background-color: #e7f5eb;
            color: #0c753a;
        }
        
        .post-type-booking .payment-status-pending {
            background-color: #fef8e3;
            color: #dfa100;
        }
        
        .post-type-booking .payment-status-failed {
            background-color: #f9e3e3;
            color: #d40000;
        }
    </style>
    <?php
}
add_action('admin_head', 'iplpro_booking_columns_style');
