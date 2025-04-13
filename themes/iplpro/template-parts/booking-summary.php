<?php
/**
 * Template part for displaying booking summary
 *
 * @package iplpro
 */

// Get match ID and seat type from parameters
$match_id = isset($args['match_id']) ? intval($args['match_id']) : 0;
$seat_type = isset($args['seat_type']) ? sanitize_text_field($args['seat_type']) : '';
$quantity = isset($args['quantity']) ? intval($args['quantity']) : 1;

// Fallback to GET parameters if args not provided
if (!$match_id && isset($_GET['match_id'])) {
    $match_id = intval($_GET['match_id']);
}
if (empty($seat_type) && isset($_GET['seat_type'])) {
    $seat_type = sanitize_text_field($_GET['seat_type']);
}
if ($quantity < 1 && isset($_GET['quantity'])) {
    $quantity = intval($_GET['quantity']);
}

// Get match details with error handling
$match = iplpro_get_match_details($match_id);

// If match is not found or data is invalid, show error
if (!$match) {
    ?>
    <div class="booking-error">
        <div class="error-message">
            <h2><?php _e('Match not found', 'iplpro'); ?></h2>
            <p><?php _e('The match you are trying to book is not available or has been removed.', 'iplpro'); ?></p>
            <a href="<?php echo esc_url(home_url('/matches')); ?>" class="back-link"><?php _e('Browse Matches', 'iplpro'); ?></a>
        </div>
    </div>
    <?php
    return;
}

// Find selected seat category
$selected_category = null;
foreach ($match['seat_categories'] as $category) {
    if ($category['type'] === $seat_type) {
        $selected_category = $category;
        break;
    }
}

// If seat type is not found or not enough seats available, show error
if (!$selected_category || $selected_category['seats_available'] < $quantity) {
    ?>
    <div class="booking-error">
        <div class="error-message">
            <h2><?php _e('Seats not available', 'iplpro'); ?></h2>
            <p><?php _e('The selected seats are no longer available. Please choose a different seat category or quantity.', 'iplpro'); ?></p>
            <a href="<?php echo esc_url(get_permalink($match_id)); ?>" class="back-link"><?php _e('Back to Match', 'iplpro'); ?></a>
        </div>
    </div>
    <?php
    return;
}

// Calculate pricing
$price_details = iplpro_calculate_total($selected_category['price'], $quantity);

// Format match date
$date_obj = new DateTime($match['date']);
$formatted_date = $date_obj->format('d F Y, g:i A T');
?>

<div class="booking-summary-box">
    <h2><?php _e('Booking Summary', 'iplpro'); ?></h2>
    
    <div class="summary-details">
        <div class="summary-row">
            <span class="label"><?php _e('Match:', 'iplpro'); ?></span>
            <span class="value"><?php echo esc_html($match['team1']->name); ?> vs <?php echo esc_html($match['team2']->name); ?></span>
        </div>
        
        <div class="summary-row">
            <span class="label"><?php _e('Date & Time:', 'iplpro'); ?></span>
            <span class="value"><?php echo esc_html($formatted_date); ?></span>
        </div>
        
        <div class="summary-row">
            <span class="label"><?php _e('Venue:', 'iplpro'); ?></span>
            <span class="value"><?php echo esc_html($match['stadium']->name); ?></span>
        </div>
        
        <div class="summary-row">
            <span class="label"><?php _e('Ticket Type:', 'iplpro'); ?></span>
            <span class="value"><?php echo esc_html($selected_category['type']); ?></span>
        </div>
        
        <div class="summary-row">
            <span class="label"><?php _e('Ticket Price:', 'iplpro'); ?></span>
            <span class="value"><?php echo iplpro_format_price($selected_category['price']); ?> <?php _e('per ticket', 'iplpro'); ?></span>
        </div>
        
        <div class="summary-row">
            <span class="label"><?php _e('Quantity:', 'iplpro'); ?></span>
            <span class="value"><?php echo esc_html($quantity); ?> <?php echo ($quantity === 1) ? __('ticket', 'iplpro') : __('tickets', 'iplpro'); ?></span>
        </div>
    </div>
    
    <div class="summary-pricing">
        <div class="price-row">
            <span class="label"><?php _e('Base Amount:', 'iplpro'); ?></span>
            <span class="value"><?php echo iplpro_format_price($price_details['base_amount']); ?></span>
        </div>
        
        <div class="price-row">
            <span class="label"><?php _e('GST (18%):', 'iplpro'); ?></span>
            <span class="value"><?php echo iplpro_format_price($price_details['gst_amount']); ?></span>
        </div>
        
        <div class="price-row">
            <span class="label"><?php _e('Service Fee:', 'iplpro'); ?></span>
            <span class="value"><?php echo iplpro_format_price($price_details['service_fee']); ?></span>
        </div>
        
        <div class="price-row total-row">
            <span class="label"><?php _e('Total Amount:', 'iplpro'); ?></span>
            <span class="value"><?php echo iplpro_format_price($price_details['total']); ?></span>
        </div>
    </div>
</div>
