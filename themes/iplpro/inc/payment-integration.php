<?php
/**
 * Payment Integration for IPL Pro Theme
 * 
 * Handles UPI payment integration, QR code generation, and UTR verification
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate a dynamic QR code for UPI payment
 * 
 * @param string $upi_id UPI ID to receive the payment
 * @param float $amount Payment amount
 * @param string $reference Transaction reference
 * @return string QR code URL
 */
function iplpro_generate_upi_qr_code($upi_id, $amount, $reference = '') {
    // Sanitize inputs
    $upi_id = sanitize_text_field($upi_id);
    $amount = floatval($amount);
    $reference = sanitize_text_field($reference);
    
    // Format the UPI URL
    $upi_url = sprintf(
        'upi://pay?pa=%s&am=%s&tn=%s&cu=INR',
        urlencode($upi_id),
        urlencode(number_format($amount, 2, '.', '')),
        urlencode('IPLTicket-' . $reference)
    );
    
    // Generate QR code using QR Code Monkey API
    $api_url = 'https://api.qrcode-monkey.com/qr/custom';
    $data = array(
        'data' => $upi_url,
        'config' => array(
            'body' => 'square',
            'eyeBall' => 'ball',
            'eyeFrame' => 'ball',
            'erf1' => array('d' => ''),
            'erf2' => array('d' => ''),
            'erf3' => array('d' => ''),
            'brf1' => array('d' => ''),
            'brf2' => array('d' => ''),
            'brf3' => array('d' => ''),
            'body_color' => '#000000',
            'bg_color' => '#FFFFFF',
            'eye1_color' => '#FF4E00',
            'eye2_color' => '#FF4E00',
            'eye3_color' => '#FF4E00',
            'frame_color' => '#FF4E00',
        ),
        'size' => 300,
        'download' => false,
        'file' => 'png'
    );
    
    // Alternative approach: generate locally using a library if installed
    if (function_exists('qr_code_generate')) {
        return qr_code_generate($upi_url, 'url', 300);
    }
    
    // If no QR library is installed, use a free third-party QR code service
    // (This is a fallback and should be replaced with a proper library in production)
    return 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($upi_url) . '&choe=UTF-8';
}

/**
 * Generate UPI app intent URL for deep linking
 * 
 * @param string $app UPI app name (gpay, phonepe, paytm, etc.)
 * @param string $upi_id UPI ID to receive payment
 * @param float $amount Payment amount
 * @param string $reference Transaction reference
 * @return string UPI intent URL
 */
function iplpro_get_upi_app_url($app, $upi_id, $amount, $reference = '') {
    // Sanitize inputs
    $app = sanitize_text_field($app);
    $upi_id = sanitize_text_field($upi_id);
    $amount = floatval($amount);
    $reference = sanitize_text_field($reference);
    
    // Base UPI URL
    $upi_url = sprintf(
        'upi://pay?pa=%s&pn=IPLTicket&am=%s&tn=%s&cu=INR',
        urlencode($upi_id),
        urlencode(number_format($amount, 2, '.', '')),
        urlencode('IPLTicket-' . $reference)
    );
    
    // App-specific intent URLs
    switch ($app) {
        case 'gpay':
            return 'tez://upi/pay?pa=' . urlencode($upi_id) . '&pn=IPLTicket&am=' . $amount . '&tn=IPLTicket-' . $reference . '&cu=INR';
        case 'phonepe':
            return 'phonepe://pay?pa=' . urlencode($upi_id) . '&pn=IPLTicket&am=' . $amount . '&tn=IPLTicket-' . $reference . '&cu=INR';
        case 'paytm':
            return 'paytmmp://pay?pa=' . urlencode($upi_id) . '&pn=IPLTicket&am=' . $amount . '&tn=IPLTicket-' . $reference . '&cu=INR';
        default:
            return $upi_url;
    }
}

/**
 * Validate UTR number format
 * 
 * @param string $utr UTR number to validate
 * @return bool True if valid, false otherwise
 */
function iplpro_validate_utr_format($utr) {
    // Sanitize the UTR
    $utr = sanitize_text_field($utr);
    
    // Check if UTR matches common format (12-22 alphanumeric characters)
    return preg_match('/^[a-zA-Z0-9]{12,22}$/', $utr);
}

/**
 * Save UTR number for an order
 * 
 * @param int $order_id Order ID
 * @param string $utr UTR number
 * @return bool True on success, false on failure
 */
function iplpro_save_utr_number($order_id, $utr) {
    // Sanitize and validate UTR
    $utr = sanitize_text_field($utr);
    
    if (!iplpro_validate_utr_format($utr)) {
        return false;
    }
    
    // Update order meta
    $result = update_post_meta($order_id, '_utr_number', $utr);
    $payment_date = current_time('mysql');
    update_post_meta($order_id, '_payment_date', $payment_date);
    
    // Update order status to pending payment verification
    wp_update_post(array(
        'ID' => $order_id,
        'post_status' => 'pending-payment'
    ));
    
    // Log the UTR submission (if WP_DEBUG is enabled)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log(sprintf('UTR submitted for order #%d: %s', $order_id, $utr));
    }
    
    return (bool) $result;
}

/**
 * Verify UTR payment (admin function)
 * 
 * @param int $order_id Order ID
 * @param bool $verified Verification status
 * @return bool True on success, false on failure
 */
function iplpro_verify_payment($order_id, $verified = true) {
    // Update verification status
    update_post_meta($order_id, '_payment_verified', $verified ? '1' : '0');
    
    // Update order status
    wp_update_post(array(
        'ID' => $order_id,
        'post_status' => $verified ? 'payment-verified' : 'pending-payment'
    ));
    
    // If verified, trigger actions
    if ($verified) {
        // Get match and ticket information
        $match_id = get_post_meta($order_id, '_match_id', true);
        $ticket_type = get_post_meta($order_id, '_ticket_type', true);
        $quantity = intval(get_post_meta($order_id, '_quantity', true));
        
        // Update available seats for the ticket type
        if ($match_id && $ticket_type && $quantity > 0) {
            iplpro_update_seats_availability($match_id, $ticket_type, $quantity);
        }
        
        // Send confirmation email
        iplpro_send_payment_confirmation_email($order_id);
        
        // Log the verification (if WP_DEBUG is enabled)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf('Payment verified for order #%d', $order_id));
        }
    }
    
    return true;
}

/**
 * Update seats availability for a match
 * 
 * @param int $match_id Match ID
 * @param string $ticket_type Ticket type
 * @param int $quantity Quantity to reduce
 * @return bool True on success, false on failure
 */
function iplpro_update_seats_availability($match_id, $ticket_type, $quantity) {
    // Get ticket categories
    $ticket_categories = get_field('ticket_categories', $match_id);
    
    if (!$ticket_categories || !is_array($ticket_categories)) {
        return false;
    }
    
    // Find the matching ticket type
    foreach ($ticket_categories as $key => $category) {
        if ($category['ticket_type'] === $ticket_type) {
            // Calculate new availability
            $current_seats = intval($category['seats_available']);
            $new_seats = max(0, $current_seats - $quantity);
            
            // Update the seats available
            $ticket_categories[$key]['seats_available'] = $new_seats;
            
            // Save the updated field
            update_field('ticket_categories', $ticket_categories, $match_id);
            
            return true;
        }
    }
    
    return false;
}

/**
 * Send payment confirmation email
 * 
 * @param int $order_id Order ID
 * @return bool True on success, false on failure
 */
function iplpro_send_payment_confirmation_email($order_id) {
    // Get order details
    $customer_name = get_post_meta($order_id, '_customer_name', true);
    $customer_email = get_post_meta($order_id, '_customer_email', true);
    
    if (!$customer_email) {
        return false;
    }
    
    // Get match details
    $match_id = get_post_meta($order_id, '_match_id', true);
    $match = get_post($match_id);
    
    if (!$match) {
        return false;
    }
    
    // Get teams information
    $home_team_id = get_field('home_team', $match_id);
    $away_team_id = get_field('away_team', $match_id);
    
    $home_team = iplpro_get_term_name($home_team_id);
    $away_team = iplpro_get_term_name($away_team_id);
    
    // Get ticket details
    $ticket_type = get_post_meta($order_id, '_ticket_type', true);
    $quantity = get_post_meta($order_id, '_quantity', true);
    $total_amount = get_post_meta($order_id, '_total_amount', true);
    
    // Prepare email content
    $subject = sprintf('Payment Confirmed - IPL Tickets for %s vs %s', $home_team, $away_team);
    
    $message = sprintf(
        "Hello %s,\n\n" .
        "Your payment for IPL tickets has been confirmed. Here are your booking details:\n\n" .
        "Order ID: IPL-%s\n" .
        "Match: %s vs %s\n" .
        "Date: %s\n" .
        "Venue: %s\n" .
        "Ticket Type: %s\n" .
        "Quantity: %s\n" .
        "Total Amount: ₹%s\n\n" .
        "You will receive your e-tickets shortly.\n\n" .
        "Thank you for your purchase!\n\n" .
        "IPL Ticket Booking Team",
        $customer_name,
        $order_id,
        $home_team,
        $away_team,
        get_field('match_date', $match_id),
        iplpro_get_term_name(get_field('stadium', $match_id), 'Stadium'),
        $ticket_type,
        $quantity,
        $total_amount
    );
    
    // Send email
    $headers = array('Content-Type: text/plain; charset=UTF-8');
    
    return wp_mail($customer_email, $subject, $message, $headers);
}

/**
 * Process UTR submission from front-end
 */
function iplpro_process_utr_submission() {
    // Check if this is a UTR submission
    if (!isset($_POST['iplpro_submit_utr']) || !isset($_POST['order_id']) || !isset($_POST['utr_number'])) {
        return;
    }
    
    // Verify nonce
    if (!isset($_POST['iplpro_utr_nonce']) || !wp_verify_nonce($_POST['iplpro_utr_nonce'], 'iplpro_submit_utr')) {
        wp_die('Security check failed', 'Error', array('response' => 403));
    }
    
    // Get and sanitize data
    $order_id = intval($_POST['order_id']);
    $utr = sanitize_text_field($_POST['utr_number']);
    
    // Validate UTR format
    if (!iplpro_validate_utr_format($utr)) {
        wp_redirect(add_query_arg('utr_error', 'invalid_format', wp_get_referer()));
        exit;
    }
    
    // Save UTR
    $result = iplpro_save_utr_number($order_id, $utr);
    
    if ($result) {
        // Redirect to confirmation page
        wp_redirect(get_permalink(get_page_by_path('order-confirmation')) . '?order_id=' . $order_id);
        exit;
    } else {
        // Redirect back with error
        wp_redirect(add_query_arg('utr_error', 'save_failed', wp_get_referer()));
        exit;
    }
}
add_action('template_redirect', 'iplpro_process_utr_submission');

/**
 * Register admin action for UTR verification
 */
function iplpro_register_admin_actions() {
    // Add UTR verification action
    add_action('admin_action_iplpro_verify_payment', function() {
        // Check if user has permission
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to perform this action.');
        }
        
        // Verify nonce
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'iplpro_verify_payment')) {
            wp_die('Security check failed.');
        }
        
        // Get order ID
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        
        if (!$order_id) {
            wp_die('Invalid order ID.');
        }
        
        // Verify payment
        iplpro_verify_payment($order_id, true);
        
        // Redirect back to order
        wp_redirect(admin_url('post.php?post=' . $order_id . '&action=edit&payment_verified=1'));
        exit;
    });
    
    // Add UTR rejection action
    add_action('admin_action_iplpro_reject_payment', function() {
        // Check if user has permission
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to perform this action.');
        }
        
        // Verify nonce
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'iplpro_reject_payment')) {
            wp_die('Security check failed.');
        }
        
        // Get order ID
        $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
        
        if (!$order_id) {
            wp_die('Invalid order ID.');
        }
        
        // Reject payment
        iplpro_verify_payment($order_id, false);
        
        // Redirect back to order
        wp_redirect(admin_url('post.php?post=' . $order_id . '&action=edit&payment_rejected=1'));
        exit;
    });
}
add_action('admin_init', 'iplpro_register_admin_actions');

/**
 * Add UTR verification admin notice
 */
function iplpro_admin_notices() {
    $screen = get_current_screen();
    
    // Only show on ticket order edit screen
    if (!$screen || $screen->base !== 'post' || $screen->post_type !== 'ticket_order') {
        return;
    }
    
    // Check for verification message
    if (isset($_GET['payment_verified'])) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Payment verified successfully.', 'iplpro'); ?></p>
        </div>
        <?php
    }
    
    // Check for rejection message
    if (isset($_GET['payment_rejected'])) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p><?php _e('Payment marked as unverified.', 'iplpro'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'iplpro_admin_notices');

/**
 * Add UTR verification meta box
 */
function iplpro_add_verification_meta_box() {
    add_meta_box(
        'iplpro_payment_verification',
        __('Payment Verification', 'iplpro'),
        'iplpro_verification_meta_box_callback',
        'ticket_order',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'iplpro_add_verification_meta_box');

/**
 * UTR verification meta box callback
 */
function iplpro_verification_meta_box_callback($post) {
    // Get verification status
    $verified = get_post_meta($post->ID, '_payment_verified', true);
    $utr = get_post_meta($post->ID, '_utr_number', true);
    
    // Output meta box content
    ?>
    <div class="payment-verification-box">
        <?php if ($utr) : ?>
            <p><strong><?php _e('UTR Number:', 'iplpro'); ?></strong> <?php echo esc_html($utr); ?></p>
            
            <?php if ($verified) : ?>
                <p class="verified-status">
                    <span class="dashicons dashicons-yes"></span>
                    <?php _e('Payment Verified', 'iplpro'); ?>
                </p>
                
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?action=iplpro_reject_payment&order_id=' . $post->ID), 'iplpro_reject_payment'); ?>" class="button button-secondary">
                        <?php _e('Mark as Unverified', 'iplpro'); ?>
                    </a>
                </p>
            <?php else : ?>
                <p class="unverified-status">
                    <span class="dashicons dashicons-warning"></span>
                    <?php _e('Payment Not Verified', 'iplpro'); ?>
                </p>
                
                <p>
                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?action=iplpro_verify_payment&order_id=' . $post->ID), 'iplpro_verify_payment'); ?>" class="button button-primary">
                        <?php _e('Verify Payment', 'iplpro'); ?>
                    </a>
                </p>
            <?php endif; ?>
        <?php else : ?>
            <p><?php _e('No UTR number submitted yet.', 'iplpro'); ?></p>
        <?php endif; ?>
    </div>
    <style>
        .payment-verification-box .verified-status {
            color: #46b450;
            font-weight: 600;
        }
        .payment-verification-box .unverified-status {
            color: #dc3232;
            font-weight: 600;
        }
    </style>
    <?php
}