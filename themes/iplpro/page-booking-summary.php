<?php
/**
 * Template for displaying booking summary
 *
 * @package iplpro
 */

// Check if form was submitted
if (!isset($_POST['match_id']) || !isset($_POST['ticket_type']) || !isset($_POST['quantity'])) {
    // If not, redirect to homepage
    wp_redirect(home_url());
    exit;
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

// Get ticket price
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

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <div class="booking-summary">
            <div class="booking-summary-header">
                <h1 class="summary-title"><?php echo esc_html__('Booking Summary', 'iplpro'); ?></h1>
                <p><?php echo esc_html__('Please review your booking details before proceeding to payment.', 'iplpro'); ?></p>
            </div>
            
            <div class="booking-info">
                <div class="booking-info-section">
                    <h3 class="booking-info-title"><?php echo esc_html__('Match Details', 'iplpro'); ?></h3>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Match:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html($match_title); ?></span>
                    </div>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Date:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html(iplpro_get_formatted_match_date($match_date)); ?></span>
                    </div>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Venue:', 'iplpro'); ?></span>
                        <span class="info-value">
                        <?php 
                        $stadium_id = get_field('stadium', $match_id);
                        echo esc_html(iplpro_get_term_name($stadium_id, 'stadium')); 
                        ?>
                        </span>
                    </div>
                </div>
                
                <div class="booking-info-section">
                    <h3 class="booking-info-title"><?php echo esc_html__('Ticket Details', 'iplpro'); ?></h3>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Ticket Type:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html($ticket_type); ?></span>
                    </div>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Price per Ticket:', 'iplpro'); ?></span>
                        <span class="info-value">₹<?php echo esc_html(number_format($price_per_ticket)); ?></span>
                    </div>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Quantity:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html($quantity); ?></span>
                    </div>
                    
                    <div class="booking-info-item total-row">
                        <span class="info-label"><?php echo esc_html__('Total Amount:', 'iplpro'); ?></span>
                        <span class="info-value booking-details-total">₹<?php echo esc_html(number_format($total_amount)); ?></span>
                    </div>
                </div>
                
                <div class="booking-info-section">
                    <h3 class="booking-info-title"><?php echo esc_html__('Customer Details', 'iplpro'); ?></h3>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Name:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html($customer_name); ?></span>
                    </div>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Email:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html($customer_email); ?></span>
                    </div>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Phone:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html($customer_phone); ?></span>
                    </div>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Address:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html($customer_address); ?></span>
                    </div>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('City:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html($customer_city); ?></span>
                    </div>
                    
                    <div class="booking-info-item">
                        <span class="info-label"><?php echo esc_html__('Pincode:', 'iplpro'); ?></span>
                        <span class="info-value"><?php echo esc_html($customer_pincode); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="booking-actions">
                <form action="<?php echo esc_url(home_url('/payment/')); ?>" method="post">
                    <!-- Hidden fields for payment page -->
                    <input type="hidden" name="match_id" value="<?php echo esc_attr($match_id); ?>">
                    <input type="hidden" name="match_title" value="<?php echo esc_attr($match_title); ?>">
                    <input type="hidden" name="match_date" value="<?php echo esc_attr($match_date); ?>">
                    
                    <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
                    <input type="hidden" name="ticket_type" value="<?php echo esc_attr($ticket_type); ?>">
                    <input type="hidden" name="quantity" value="<?php echo esc_attr($quantity); ?>">
                    
                    <input type="hidden" name="customer_name" value="<?php echo esc_attr($customer_name); ?>">
                    <input type="hidden" name="customer_email" value="<?php echo esc_attr($customer_email); ?>">
                    <input type="hidden" name="customer_phone" value="<?php echo esc_attr($customer_phone); ?>">
                    <input type="hidden" name="customer_address" value="<?php echo esc_attr($customer_address); ?>">
                    <input type="hidden" name="customer_city" value="<?php echo esc_attr($customer_city); ?>">
                    <input type="hidden" name="customer_pincode" value="<?php echo esc_attr($customer_pincode); ?>">
                    
                    <input type="hidden" name="action" value="proceed_to_payment">
                    
                    <a href="javascript:history.back();" class="back-btn"><?php echo esc_html__('Back', 'iplpro'); ?></a>
                    <button type="submit" class="proceed-btn">
                        <?php echo esc_html__('Proceed to Payment', 'iplpro'); ?>
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();