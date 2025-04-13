<?php
/**
 * Payment Integration for IPL Pro theme
 *
 * @package iplpro
 */

/**
 * Payment processor class
 */
class IPLPro_Payment {
    /**
     * Process payment for a booking
     */
    public static function process_payment($order_id, $payment_method, $payment_data = array()) {
        // Get order information
        $order_post = get_post($order_id);
        
        if (!$order_post || $order_post->post_type !== 'order') {
            return array(
                'success' => false,
                'message' => __('Invalid order ID.', 'iplpro')
            );
        }
        
        // Get payment fields
        $match_id = get_field('match_id', $order_id);
        $ticket_type = get_field('ticket_type', $order_id);
        $quantity = get_field('quantity', $order_id);
        $total_amount = get_field('total_amount', $order_id);
        $customer_name = get_field('customer_name', $order_id);
        $customer_email = get_field('customer_email', $order_id);
        $customer_phone = get_field('customer_phone', $order_id);
        
        // Validate required fields
        if (!$match_id || !$ticket_type || !$quantity || !$total_amount || !$customer_name || !$customer_email || !$customer_phone) {
            return array(
                'success' => false,
                'message' => __('Missing required order information.', 'iplpro')
            );
        }
        
        // Process based on payment method
        switch ($payment_method) {
            case 'razorpay':
                return self::process_razorpay_payment($order_id, $total_amount, $payment_data);
            
            case 'upi':
                return self::process_upi_payment($order_id, $payment_data);
            
            case 'bank_transfer':
                return self::process_bank_transfer($order_id, $payment_data);
            
            default:
                return array(
                    'success' => false,
                    'message' => __('Invalid payment method.', 'iplpro')
                );
        }
    }
    
    /**
     * Process Razorpay payment
     */
    private static function process_razorpay_payment($order_id, $total_amount, $payment_data) {
        // Check for payment ID
        if (empty($payment_data['razorpay_payment_id'])) {
            return array(
                'success' => false,
                'message' => __('Razorpay payment ID is missing.', 'iplpro')
            );
        }
        
        // Here, you would normally verify the payment with Razorpay API
        // For now, we'll just update the order status
        
        // Set payment reference
        update_field('payment_reference', $payment_data['razorpay_payment_id'], $order_id);
        
        // Set order status to completed
        wp_set_object_terms($order_id, 'completed', 'order_status');
        
        return array(
            'success' => true,
            'message' => __('Payment successful!', 'iplpro'),
            'redirect' => add_query_arg(array(
                'order_id' => $order_id,
                'payment_method' => 'razorpay',
                'payment_status' => 'success'
            ), site_url('/order-confirmation/'))
        );
    }
    
    /**
     * Process UPI payment
     */
    private static function process_upi_payment($order_id, $payment_data) {
        // Check for UTR number
        if (empty($payment_data['utr_number'])) {
            return array(
                'success' => false,
                'message' => __('UTR number is missing.', 'iplpro')
            );
        }
        
        // Set payment reference
        update_field('payment_reference', $payment_data['utr_number'], $order_id);
        
        // UPI payments need verification, so set status to processing
        wp_set_object_terms($order_id, 'processing', 'order_status');
        
        return array(
            'success' => true,
            'message' => __('UTR number submitted successfully. Your payment is being verified.', 'iplpro'),
            'redirect' => add_query_arg(array(
                'order_id' => $order_id,
                'payment_method' => 'upi',
                'payment_status' => 'processing'
            ), site_url('/order-confirmation/'))
        );
    }
    
    /**
     * Process bank transfer payment
     */
    private static function process_bank_transfer($order_id, $payment_data) {
        // Check for transfer reference
        if (empty($payment_data['transfer_reference'])) {
            return array(
                'success' => false,
                'message' => __('Transfer reference is missing.', 'iplpro')
            );
        }
        
        // Set payment reference
        update_field('payment_reference', $payment_data['transfer_reference'], $order_id);
        
        // Bank transfers need verification, so set status to processing
        wp_set_object_terms($order_id, 'processing', 'order_status');
        
        return array(
            'success' => true,
            'message' => __('Bank transfer reference submitted successfully. Your payment is being verified.', 'iplpro'),
            'redirect' => add_query_arg(array(
                'order_id' => $order_id,
                'payment_method' => 'bank_transfer',
                'payment_status' => 'processing'
            ), site_url('/order-confirmation/'))
        );
    }
}

/**
 * Save booking data and create order
 */
function iplpro_save_booking() {
    // Check if it's a booking submission
    if (!isset($_POST['match_id']) || !isset($_POST['ticket_type']) || !isset($_POST['quantity'])) {
        return;
    }
    
    // Get form data
    $match_id = sanitize_text_field($_POST['match_id']);
    $match_title = sanitize_text_field($_POST['match_title']);
    $match_date = sanitize_text_field($_POST['match_date']);
    $order_id = sanitize_text_field($_POST['order_id']);
    $ticket_type = sanitize_text_field($_POST['ticket_type']);
    $quantity = intval($_POST['quantity']);
    
    // Get customer details
    $customer_name = sanitize_text_field($_POST['customer_name']);
    $customer_email = sanitize_email($_POST['customer_email']);
    $customer_phone = sanitize_text_field($_POST['customer_phone']);
    $customer_address = sanitize_text_field($_POST['customer_address']);
    $customer_city = sanitize_text_field($_POST['customer_city']);
    $customer_pincode = sanitize_text_field($_POST['customer_pincode']);
    
    // Calculate price based on ticket type
    $price_per_ticket = 0;
    $ticket_types = get_field('ticket_types', $match_id);
    
    if (!empty($ticket_types)) {
        foreach ($ticket_types as $ticket) {
            if ($ticket['ticket_name'] === $ticket_type) {
                $price_per_ticket = floatval($ticket['ticket_price']);
                break;
            }
        }
    }
    
    // If no price found, use default pricing
    if ($price_per_ticket <= 0) {
        $default_prices = array(
            'Premium' => 7999,
            'Corporate Box' => 4999,
            'East Stand' => 2999,
            'West Stand' => 2999,
            'North Stand' => 1499,
            'South Stand' => 1499,
        );
        
        $price_per_ticket = isset($default_prices[$ticket_type]) ? $default_prices[$ticket_type] : 1499;
    }
    
    // Calculate total amount
    $total_amount = $price_per_ticket * $quantity;
    
    // Create or update order post
    $order_exists = false;
    
    // Check if order with this ID exists
    $existing_orders = get_posts(array(
        'post_type' => 'order',
        'meta_key' => 'order_id',
        'meta_value' => $order_id,
        'posts_per_page' => 1
    ));
    
    if (!empty($existing_orders)) {
        $order_post_id = $existing_orders[0]->ID;
        $order_exists = true;
    } else {
        // Create new order post
        $order_post_id = wp_insert_post(array(
            'post_title' => $order_id,
            'post_type' => 'order',
            'post_status' => 'publish'
        ));
    }
    
    if (!$order_post_id) {
        // Handle error
        wp_redirect(site_url());
        exit;
    }
    
    // Update order custom fields
    update_field('order_id', $order_id, $order_post_id);
    update_field('match_id', $match_id, $order_post_id);
    update_field('match_title', $match_title, $order_post_id);
    update_field('match_date', $match_date, $order_post_id);
    update_field('ticket_type', $ticket_type, $order_post_id);
    update_field('quantity', $quantity, $order_post_id);
    update_field('price_per_ticket', $price_per_ticket, $order_post_id);
    update_field('total_amount', $total_amount, $order_post_id);
    update_field('customer_name', $customer_name, $order_post_id);
    update_field('customer_email', $customer_email, $order_post_id);
    update_field('customer_phone', $customer_phone, $order_post_id);
    update_field('customer_address', $customer_address, $order_post_id);
    update_field('customer_city', $customer_city, $order_post_id);
    update_field('customer_pincode', $customer_pincode, $order_post_id);
    
    // Set order status to pending payment
    if (!$order_exists) {
        wp_set_object_terms($order_post_id, 'pending-payment', 'order_status');
    }
    
    // Save order data in session for payment page
    if (!session_id()) {
        session_start();
    }
    
    $_SESSION['iplpro_order'] = array(
        'order_id' => $order_id,
        'order_post_id' => $order_post_id,
        'match_title' => $match_title,
        'ticket_type' => $ticket_type,
        'quantity' => $quantity,
        'price_per_ticket' => $price_per_ticket,
        'total_amount' => $total_amount,
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $customer_phone
    );
    
    // Redirect to payment page
    if (isset($_POST['action']) && $_POST['action'] === 'proceed_to_payment') {
        wp_redirect(add_query_arg('order_id', $order_id, site_url('/payment/')));
        exit;
    }
}
add_action('template_redirect', 'iplpro_save_booking');

/**
 * Process payment submission
 */
function iplpro_process_payment_submission() {
    // Check if it's a payment submission
    if (!isset($_POST['payment_method']) || !isset($_POST['order_id'])) {
        return;
    }
    
    $order_id = sanitize_text_field($_POST['order_id']);
    $payment_method = sanitize_text_field($_POST['payment_method']);
    
    // Get order post ID
    $order_posts = get_posts(array(
        'post_type' => 'order',
        'meta_key' => 'order_id',
        'meta_value' => $order_id,
        'posts_per_page' => 1
    ));
    
    if (empty($order_posts)) {
        wp_redirect(site_url());
        exit;
    }
    
    $order_post_id = $order_posts[0]->ID;
    
    // Process based on payment method
    $payment_data = array();
    
    if ($payment_method === 'razorpay') {
        if (isset($_POST['razorpay_payment_id'])) {
            $payment_data['razorpay_payment_id'] = sanitize_text_field($_POST['razorpay_payment_id']);
        }
    } elseif ($payment_method === 'upi') {
        if (isset($_POST['utr_number'])) {
            $payment_data['utr_number'] = sanitize_text_field($_POST['utr_number']);
        }
    } elseif ($payment_method === 'bank_transfer') {
        if (isset($_POST['transfer_reference'])) {
            $payment_data['transfer_reference'] = sanitize_text_field($_POST['transfer_reference']);
        }
    }
    
    // Process payment
    $result = IPLPro_Payment::process_payment($order_post_id, $payment_method, $payment_data);
    
    if ($result['success']) {
        // Update order status and redirect
        wp_redirect($result['redirect']);
    } else {
        // Show error and redirect back to payment page
        wp_redirect(add_query_arg(array(
            'order_id' => $order_id,
            'payment_error' => urlencode($result['message'])
        ), site_url('/payment/')));
    }
    
    exit;
}
add_action('template_redirect', 'iplpro_process_payment_submission');

/**
 * Generate Razorpay order for checkout
 */
function iplpro_generate_razorpay_order($order_id, $total_amount) {
    // In a real implementation, this would interact with Razorpay API
    // For this demo, we'll just return a mock order ID
    return 'order_' . uniqid();
}

/**
 * Get payment details for order confirmation
 */
function iplpro_get_payment_details($order_id) {
    // Get order post
    $order_posts = get_posts(array(
        'post_type' => 'order',
        'meta_key' => 'order_id',
        'meta_value' => $order_id,
        'posts_per_page' => 1
    ));
    
    if (empty($order_posts)) {
        return false;
    }
    
    $order_post_id = $order_posts[0]->ID;
    
    // Get payment and order details
    $match_id = get_field('match_id', $order_post_id);
    $match_title = get_field('match_title', $order_post_id);
    $match_date = get_field('match_date', $order_post_id);
    $ticket_type = get_field('ticket_type', $order_post_id);
    $quantity = get_field('quantity', $order_post_id);
    $price_per_ticket = get_field('price_per_ticket', $order_post_id);
    $total_amount = get_field('total_amount', $order_post_id);
    $customer_name = get_field('customer_name', $order_post_id);
    $customer_email = get_field('customer_email', $order_post_id);
    $customer_phone = get_field('customer_phone', $order_post_id);
    $payment_reference = get_field('payment_reference', $order_post_id);
    
    // Get order status
    $order_status_terms = get_the_terms($order_post_id, 'order_status');
    $order_status = !empty($order_status_terms) ? $order_status_terms[0]->name : 'Pending Payment';
    
    return array(
        'order_id' => $order_id,
        'order_post_id' => $order_post_id,
        'match_id' => $match_id,
        'match_title' => $match_title,
        'match_date' => $match_date,
        'ticket_type' => $ticket_type,
        'quantity' => $quantity,
        'price_per_ticket' => $price_per_ticket,
        'total_amount' => $total_amount,
        'customer_name' => $customer_name,
        'customer_email' => $customer_email,
        'customer_phone' => $customer_phone,
        'payment_reference' => $payment_reference,
        'order_status' => $order_status,
    );
}

/**
 * Add Razorpay payment scripts
 */
function iplpro_enqueue_payment_scripts() {
    if (is_page('payment')) {
        wp_enqueue_script('razorpay', 'https://checkout.razorpay.com/v1/checkout.js', array(), null, true);
    }
}
add_action('wp_enqueue_scripts', 'iplpro_enqueue_payment_scripts');