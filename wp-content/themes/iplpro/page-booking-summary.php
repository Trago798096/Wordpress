<?php
/**
 * Template Name: Booking Summary
 *
 * This is the template for the booking summary page
 *
 * @package iplpro
 */

get_header();

// Get booking details from query parameters
$match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
$seat_type = isset($_GET['seat_type']) ? sanitize_text_field($_GET['seat_type']) : '';
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;

// Get match details with error handling
$match = iplpro_get_match_details($match_id);

// If match is not found or data is invalid, redirect to matches page
if (!$match) {
    wp_redirect(home_url('/matches'));
    exit;
}

// Find selected seat category
$selected_category = null;
foreach ($match['seat_categories'] as $category) {
    if ($category['type'] === $seat_type) {
        $selected_category = $category;
        break;
    }
}

// If seat type is not found or not enough seats available, redirect
if (!$selected_category || $selected_category['seats_available'] < $quantity) {
    wp_redirect(home_url('/matches'));
    exit;
}

// Calculate pricing
$price_details = iplpro_calculate_total($selected_category['price'], $quantity);
?>

<div id="primary" class="content-area booking-summary-content">
    <main id="main" class="site-main">

        <div class="booking-summary-container">
            <div class="page-header">
                <h1>Complete Your Booking</h1>
            </div>
            
            <div class="booking-summary">
                <h2>Booking Summary</h2>
                
                <div class="summary-details">
                    <div class="summary-row">
                        <span class="label">Match:</span>
                        <span class="value"><?php echo esc_html($match['team1']->name); ?> vs <?php echo esc_html($match['team2']->name); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Date & Time:</span>
                        <span class="value">
                            <?php 
                            $date_obj = new DateTime($match['date']);
                            echo esc_html($date_obj->format('d F Y, g:i A T')); 
                            ?>
                        </span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Venue:</span>
                        <span class="value"><?php echo esc_html($match['stadium']->name); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Ticket Type:</span>
                        <span class="value"><?php echo esc_html($selected_category['type']); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Ticket Price:</span>
                        <span class="value"><?php echo iplpro_format_price($selected_category['price']); ?> per ticket</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Quantity:</span>
                        <span class="value"><?php echo esc_html($quantity); ?> tickets</span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Base Amount:</span>
                        <span class="value"><?php echo iplpro_format_price($price_details['base_amount']); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">GST (18%):</span>
                        <span class="value"><?php echo iplpro_format_price($price_details['gst_amount']); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span class="label">Service Fee:</span>
                        <span class="value"><?php echo iplpro_format_price($price_details['service_fee']); ?></span>
                    </div>
                    
                    <div class="summary-row total-row">
                        <span class="label">Total Amount:</span>
                        <span class="value"><?php echo iplpro_format_price($price_details['total']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="customer-info">
                <h2>Customer Information</h2>
                
                <form id="customer-info-form" action="<?php echo esc_url(home_url('/payment')); ?>" method="get">
                    <input type="hidden" name="match_id" value="<?php echo esc_attr($match_id); ?>">
                    <input type="hidden" name="seat_type" value="<?php echo esc_attr($seat_type); ?>">
                    <input type="hidden" name="quantity" value="<?php echo esc_attr($quantity); ?>">
                    <input type="hidden" name="total" value="<?php echo esc_attr($price_details['total']); ?>">
                    
                    <div class="form-group">
                        <label for="fullname">Full Name</label>
                        <input type="text" id="fullname" name="fullname" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required pattern="[0-9]{10}">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="payment-btn">Pay with Razorpay ₹<?php echo number_format($price_details['total'], 2); ?></button>
                    </div>
                    
                    <div class="terms-notice">
                        <p>By proceeding, you agree to our Terms & Conditions</p>
                    </div>
                </form>
            </div>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
