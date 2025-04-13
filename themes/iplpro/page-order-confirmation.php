<?php
/**
 * Template for displaying order confirmation page
 *
 * @package iplpro
 */

// Check if we have order ID in query string
if (!isset($_GET['order_id'])) {
    // If not, redirect to homepage
    wp_redirect(home_url());
    exit;
}

// Get order ID
$order_id = sanitize_text_field($_GET['order_id']);

// Get order details
$order_details = iplpro_get_payment_details($order_id);

if (!$order_details) {
    // If order not found, redirect to homepage
    wp_redirect(home_url());
    exit;
}

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="order-confirmation">
            <div class="confirmation-icon">
                <span class="dashicons dashicons-yes-alt"></span>
            </div>
            
            <h1 class="confirmation-title"><?php echo esc_html__('Thank You for Your Booking!', 'iplpro'); ?></h1>
            
            <div class="confirmation-message">
                <p><?php echo esc_html__('Your ticket booking has been received. Details of your booking are shown below.', 'iplpro'); ?></p>
                <p><?php echo esc_html__('A confirmation email has been sent to your registered email address.', 'iplpro'); ?></p>
            </div>
            
            <div class="booking-id">
                <strong><?php echo esc_html__('Booking ID:', 'iplpro'); ?></strong> 
                <span><?php echo esc_html($order_details['order_id']); ?></span>
            </div>
            
            <div class="order-details">
                <div class="match-info">
                    <h3><?php echo esc_html__('Match Details', 'iplpro'); ?></h3>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Match:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['match_title']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Date:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html(iplpro_get_formatted_match_date($order_details['match_date'])); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Venue:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['stadium']); ?></span>
                    </div>
                    
                    <h3><?php echo esc_html__('Ticket Details', 'iplpro'); ?></h3>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Ticket Type:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['ticket_type']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Quantity:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['quantity']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Price per Ticket:', 'iplpro'); ?></span>
                        <span class="detail-value">₹<?php echo esc_html(number_format($order_details['price_per_ticket'])); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Total Amount:', 'iplpro'); ?></span>
                        <span class="detail-value">₹<?php echo esc_html(number_format($order_details['total_amount'])); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Payment Method:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html(iplpro_get_payment_method_text($_GET['payment_method'] ?? '')); ?></span>
                    </div>
                </div>
                
                <div class="customer-info">
                    <h3><?php echo esc_html__('Customer Details', 'iplpro'); ?></h3>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Name:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['customer_name']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Email:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['customer_email']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Phone:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['customer_phone']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Address:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['customer_address']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('City:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['customer_city']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label"><?php echo esc_html__('Pincode:', 'iplpro'); ?></span>
                        <span class="detail-value"><?php echo esc_html($order_details['customer_pincode']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="ticket-info">
                <h3><?php echo esc_html__('Important Information', 'iplpro'); ?></h3>
                
                <div class="ticket-info-item">
                    <?php echo esc_html__('Please arrive at the venue at least 1 hour before the match starts.', 'iplpro'); ?>
                </div>
                
                <div class="ticket-info-item">
                    <?php echo esc_html__('Bring a copy of this confirmation along with a valid ID proof.', 'iplpro'); ?>
                </div>
            </div>
            
            <div class="confirmation-actions">
                <a href="<?php echo esc_url(home_url()); ?>" class="home-btn"><?php echo esc_html__('Back to Home', 'iplpro'); ?></a>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();