<?php
/**
 * Custom template tags for this theme
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function iplpro_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    
    if (get_the_time('U') !== get_the_modified_time('U')) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf(
        $time_string,
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date()),
        esc_attr(get_the_modified_date(DATE_W3C)),
        esc_html(get_the_modified_date())
    );

    $posted_on = sprintf(
        /* translators: %s: post date. */
        esc_html_x('Posted on %s', 'post date', 'iplpro'),
        '<a href="' . esc_url(get_permalink()) . '" rel="bookmark">' . $time_string . '</a>'
    );

    echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Prints HTML with meta information for the current author.
 */
function iplpro_posted_by() {
    $byline = sprintf(
        /* translators: %s: post author. */
        esc_html_x('by %s', 'post author', 'iplpro'),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
    );

    echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function iplpro_entry_footer() {
    // Hide category and tag text for pages.
    if ('post' === get_post_type()) {
        /* translators: used between list items, there is a space after the comma */
        $categories_list = get_the_category_list(esc_html__(', ', 'iplpro'));
        if ($categories_list) {
            /* translators: 1: list of categories. */
            printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'iplpro') . '</span>', $categories_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }

        /* translators: used between list items, there is a space after the comma */
        $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'iplpro'));
        if ($tags_list) {
            /* translators: 1: list of tags. */
            printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'iplpro') . '</span>', $tags_list); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
    }

    if (!is_single() && !post_password_required() && (comments_open() || get_comments_number())) {
        echo '<span class="comments-link">';
        comments_popup_link(
            sprintf(
                wp_kses(
                    /* translators: %s: post title */
                    __('Leave a Comment<span class="screen-reader-text"> on %s</span>', 'iplpro'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            )
        );
        echo '</span>';
    }

    edit_post_link(
        sprintf(
            wp_kses(
                /* translators: %s: Name of current post. Only visible to screen readers */
                __('Edit <span class="screen-reader-text">%s</span>', 'iplpro'),
                array(
                    'span' => array(
                        'class' => array(),
                    ),
                )
            ),
            wp_kses_post(get_the_title())
        ),
        '<span class="edit-link">',
        '</span>'
    );
}

/**
 * Displays an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 */
function iplpro_post_thumbnail() {
    if (post_password_required() || is_attachment() || !has_post_thumbnail()) {
        return;
    }

    if (is_singular()) :
        ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail(); ?>
        </div><!-- .post-thumbnail -->
    <?php else : ?>
        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php
                the_post_thumbnail(
                    'post-thumbnail',
                    array(
                        'alt' => the_title_attribute(
                            array(
                                'echo' => false,
                            )
                        ),
                    )
                );
            ?>
        </a>
    <?php endif; // End is_singular().
}

/**
 * Display match details
 * 
 * @param int $match_id Match ID
 */
function iplpro_match_details($match_id = null) {
    if (!$match_id) {
        $match_id = get_the_ID();
    }
    
    $home_team_id = get_field('home_team', $match_id);
    $away_team_id = get_field('away_team', $match_id);
    $stadium_id = get_field('stadium', $match_id);
    $match_date = get_field('match_date', $match_id);
    
    $home_team = iplpro_get_term_name($home_team_id);
    $away_team = iplpro_get_term_name($away_team_id);
    $stadium = iplpro_get_term_name($stadium_id, 'Stadium');
    
    if (function_exists('iplpro_format_match_date')) {
        $formatted_date = iplpro_format_match_date($match_date);
    } else {
        $formatted_date = $match_date;
    }
    
    ?>
    <div class="match-details">
        <div class="match-header">
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
                    <h3 class="team-name"><?php echo esc_html($home_team); ?></h3>
                </div>
                
                <div class="vs-container">
                    <span class="vs-text">vs</span>
                </div>
                
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
                    <h3 class="team-name"><?php echo esc_html($away_team); ?></h3>
                </div>
            </div>
        </div>
        
        <div class="match-meta">
            <div class="match-date-time">
                <span class="meta-icon"><i class="dashicons dashicons-calendar-alt"></i></span>
                <span class="meta-text"><?php echo esc_html($formatted_date); ?></span>
            </div>
            
            <div class="match-venue">
                <span class="meta-icon"><i class="dashicons dashicons-location"></i></span>
                <span class="meta-text"><?php echo esc_html($stadium); ?></span>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Display ticket categories
 * 
 * @param int $match_id Match ID
 */
function iplpro_ticket_categories($match_id = null) {
    if (!$match_id) {
        $match_id = get_the_ID();
    }
    
    $ticket_categories = get_field('ticket_categories', $match_id);
    
    if (!$ticket_categories || !is_array($ticket_categories)) {
        echo '<p class="no-tickets">' . __('No tickets available for this match.', 'iplpro') . '</p>';
        return;
    }
    
    ?>
    <div class="ticket-categories">
        <h3 class="ticket-categories-title"><?php _e('Select a ticket type', 'iplpro'); ?></h3>
        <p class="ticket-categories-subtitle"><?php _e('Choose from our available ticket categories', 'iplpro'); ?></p>
        
        <div class="ticket-categories-grid">
            <?php foreach ($ticket_categories as $category) : ?>
                <div class="ticket-type-card" data-ticket-type="<?php echo esc_attr($category['ticket_type']); ?>" data-price="<?php echo esc_attr($category['ticket_price']); ?>">
                    <div class="ticket-type-header">
                        <h4 class="ticket-type-name"><?php echo esc_html($category['ticket_type']); ?></h4>
                        <div class="ticket-price">₹<?php echo esc_html($category['ticket_price']); ?></div>
                    </div>
                    <div class="ticket-type-body">
                        <p class="ticket-description"><?php echo esc_html($category['ticket_description']); ?></p>
                        <p class="ticket-availability">
                            <span class="availability-count"><?php echo esc_html($category['seats_available']); ?></span>
                            <?php _e('seats available', 'iplpro'); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Display stadium map
 */
function iplpro_stadium_map() {
    $stadium_map_file = get_template_directory() . '/assets/svg/stadium-map.svg';
    
    if (file_exists($stadium_map_file)) {
        ?>
        <div class="stadium-map-container">
            <h3 class="stadium-map-title"><?php _e('Select a section from the stadium map', 'iplpro'); ?></h3>
            <div class="stadium-map">
                <?php include($stadium_map_file); ?>
            </div>
            <div class="selection-message" style="display: none;"></div>
        </div>
        <?php
    }
}

/**
 * Display booking form
 * 
 * @param int $match_id Match ID
 */
function iplpro_booking_form($match_id = null) {
    if (!$match_id) {
        $match_id = get_the_ID();
    }
    
    ?>
    <div class="booking-summary">
        <h3 class="booking-summary-title"><?php _e('Booking Summary', 'iplpro'); ?></h3>
        
        <div class="booking-fields">
            <div class="ticket-selection">
                <div class="ticket-type-field">
                    <label><?php _e('Ticket Type', 'iplpro'); ?></label>
                    <div class="selected-ticket-type"></div>
                </div>
                
                <div class="ticket-price-field">
                    <label><?php _e('Price per Ticket:', 'iplpro'); ?></label>
                    <div class="ticket-price" data-price="0">₹0</div>
                </div>
                
                <div class="ticket-quantity-field">
                    <label><?php _e('Quantity:', 'iplpro'); ?></label>
                    <div class="quantity-control">
                        <button type="button" class="quantity-btn minus">-</button>
                        <input type="number" name="quantity" value="1" min="1" max="10">
                        <button type="button" class="quantity-btn plus">+</button>
                    </div>
                </div>
            </div>
            
            <div class="booking-total">
                <div class="total-label"><?php _e('Total:', 'iplpro'); ?></div>
                <div class="total-amount">₹0</div>
            </div>
        </div>
        
        <form method="post" class="booking-form">
            <?php wp_nonce_field('iplpro_booking_form', 'iplpro_booking_nonce'); ?>
            <input type="hidden" name="match_id" value="<?php echo esc_attr($match_id); ?>">
            <input type="hidden" name="ticket_type" value="">
            
            <?php iplpro_display_booking_errors(); ?>
            
            <div class="form-fields">
                <div class="form-row">
                    <div class="form-field">
                        <label for="customer_name"><?php _e('Full Name', 'iplpro'); ?></label>
                        <input type="text" name="customer_name" id="customer_name" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-field">
                        <label for="customer_email"><?php _e('Email Address', 'iplpro'); ?></label>
                        <input type="email" name="customer_email" id="customer_email" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-field">
                        <label for="customer_phone"><?php _e('Phone Number', 'iplpro'); ?></label>
                        <input type="tel" name="customer_phone" id="customer_phone" required>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <p class="terms-text"><?php _e('By proceeding, you agree to our Terms & Conditions', 'iplpro'); ?></p>
                <button type="submit" name="iplpro_booking_submit" class="booking-submit-btn"><?php _e('Proceed to Payment', 'iplpro'); ?></button>
            </div>
        </form>
    </div>
    <?php
}

/**
 * Display payment options
 * 
 * @param int $order_id Order ID
 */
function iplpro_payment_options($order_id) {
    $order = get_post($order_id);
    
    if (!$order || $order->post_type !== 'ticket_order') {
        return;
    }
    
    $match_id = get_post_meta($order_id, '_match_id', true);
    $total_amount = get_post_meta($order_id, '_total_amount', true);
    $enable_upi = get_field('enable_upi', 'option');
    $enable_cards = get_field('enable_cards', 'option');
    $enable_wallet = get_field('enable_wallet', 'option');
    $upi_id = get_field('upi_id', 'option');
    
    if (empty($upi_id)) {
        $upi_id = 'ipl@upi';
    }
    
    ?>
    <div class="payment-options">
        <h2 class="payment-title"><?php _e('Payment Options', 'iplpro'); ?></h2>
        <p class="payment-subtitle"><?php _e('All Payment Options', 'iplpro'); ?></p>
        
        <div class="payment-tabs">
            <?php if ($enable_upi) : ?>
                <div class="payment-tab active" data-tab="upi">
                    <span class="payment-icon upi-icon"></span>
                    <span class="payment-text">UPI/QR</span>
                </div>
            <?php endif; ?>
            
            <?php if ($enable_cards) : ?>
                <div class="payment-tab" data-tab="cards">
                    <span class="payment-icon card-icon"></span>
                    <span class="payment-text">Cards</span>
                </div>
            <?php endif; ?>
            
            <?php if ($enable_wallet) : ?>
                <div class="payment-tab" data-tab="wallet">
                    <span class="payment-icon wallet-icon"></span>
                    <span class="payment-text">Wallet</span>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="payment-tab-content">
            <?php if ($enable_upi) : ?>
                <div class="tab-pane active" id="upi-tab">
                    <p class="upi-info"><?php _e('Pay using any UPI App', 'iplpro'); ?></p>
                    
                    <div class="qr-code-container">
                        <?php
                        // Get QR code from settings
                        $qr_code = get_field('qr_code', 'option');
                        
                        if ($qr_code) {
                            // Use uploaded QR code
                            echo '<img src="' . esc_url($qr_code) . '" alt="Scan QR code to pay" class="qr-code-image">';
                        } else {
                            // Generate dynamic QR code
                            $qr_url = iplpro_generate_upi_qr_code($upi_id, $total_amount, $order_id);
                            echo '<img src="' . esc_url($qr_url) . '" alt="Scan QR code to pay" class="qr-code-image">';
                        }
                        ?>
                        <p class="scan-text"><?php _e('Scan the QR using any UPI App', 'iplpro'); ?></p>
                    </div>
                    
                    <div class="upi-apps">
                        <p class="pay-with-upi"><?php _e('Pay with UPI Apps', 'iplpro'); ?></p>
                        <div class="upi-app-buttons">
                            <a href="<?php echo esc_url(iplpro_get_upi_app_url('gpay', $upi_id, $total_amount, $order_id)); ?>" class="upi-app-button gpay">
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/gpay.png'); ?>" alt="Google Pay">
                            </a>
                            <a href="<?php echo esc_url(iplpro_get_upi_app_url('phonepe', $upi_id, $total_amount, $order_id)); ?>" class="upi-app-button phonepe">
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/phonepe.png'); ?>" alt="PhonePe">
                            </a>
                            <a href="<?php echo esc_url(iplpro_get_upi_app_url('paytm', $upi_id, $total_amount, $order_id)); ?>" class="upi-app-button paytm">
                                <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/paytm.png'); ?>" alt="Paytm">
                            </a>
                        </div>
                    </div>
                    
                    <div class="order-reference">
                        <p class="reference-id">
                            <span class="ref-label"><?php _e('Reference ID:', 'iplpro'); ?></span>
                            <span class="ref-value" id="reference-id">IPL-<?php echo esc_html($order_id); ?></span>
                            <button type="button" class="copy-btn" data-copy="IPL-<?php echo esc_html($order_id); ?>">
                                <?php _e('Copy', 'iplpro'); ?>
                            </button>
                        </p>
                        <p class="amount-label"><?php _e('Amount to be Paid', 'iplpro'); ?> <span class="amount-value">₹<?php echo esc_html($total_amount); ?></span></p>
                    </div>
                    
                    <form method="post" class="utr-form">
                        <?php wp_nonce_field('iplpro_submit_utr', 'iplpro_utr_nonce'); ?>
                        <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
                        
                        <div class="utr-field">
                            <label for="utr_number"><?php _e('UTR No/Transaction Number', 'iplpro'); ?></label>
                            <input type="text" name="utr_number" id="utr_number" placeholder="<?php esc_attr_e('Enter 12-digit UTR number', 'iplpro'); ?>" required>
                            <p class="utr-help"><?php _e('UTR number is a unique code generated for each UPI transaction', 'iplpro'); ?></p>
                        </div>
                        
                        <div class="utr-submit">
                            <button type="submit" name="iplpro_submit_utr" class="utr-submit-btn"><?php _e('Submit & Complete Order', 'iplpro'); ?></button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
            
            <?php if ($enable_cards) : ?>
                <div class="tab-pane" id="cards-tab">
                    <p class="coming-soon"><?php _e('Card payments coming soon. Please use UPI for now.', 'iplpro'); ?></p>
                </div>
            <?php endif; ?>
            
            <?php if ($enable_wallet) : ?>
                <div class="tab-pane" id="wallet-tab">
                    <p class="coming-soon"><?php _e('Wallet payments coming soon. Please use UPI for now.', 'iplpro'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Display order confirmation
 * 
 * @param int $order_id Order ID
 */
function iplpro_order_confirmation($order_id) {
    $order = get_post($order_id);
    
    if (!$order || $order->post_type !== 'ticket_order') {
        return;
    }
    
    $match_id = get_post_meta($order_id, '_match_id', true);
    $customer_name = get_post_meta($order_id, '_customer_name', true);
    $customer_email = get_post_meta($order_id, '_customer_email', true);
    $ticket_type = get_post_meta($order_id, '_ticket_type', true);
    $quantity = get_post_meta($order_id, '_quantity', true);
    $unit_price = get_post_meta($order_id, '_unit_price', true);
    $total_amount = get_post_meta($order_id, '_total_amount', true);
    $utr_number = get_post_meta($order_id, '_utr_number', true);
    
    $match = get_post($match_id);
    $home_team_id = get_field('home_team', $match_id);
    $away_team_id = get_field('away_team', $match_id);
    $stadium_id = get_field('stadium', $match_id);
    $match_date = get_field('match_date', $match_id);
    
    $home_team = iplpro_get_term_name($home_team_id);
    $away_team = iplpro_get_term_name($away_team_id);
    $stadium = iplpro_get_term_name($stadium_id, 'Stadium');
    
    ?>
    <div class="order-confirmation">
        <div class="confirmation-icon">
            <i class="dashicons dashicons-yes-alt"></i>
        </div>
        
        <h2 class="confirmation-title"><?php _e('Order Received!', 'iplpro'); ?></h2>
        <p class="confirmation-subtitle"><?php _e('Thank you for your order. Your payment will be verified shortly.', 'iplpro'); ?></p>
        
        <div class="order-details">
            <div class="order-number">
                <span class="detail-label"><?php _e('Order ID:', 'iplpro'); ?></span>
                <span class="detail-value">IPL-<?php echo esc_html($order_id); ?></span>
            </div>
            
            <div class="transaction-number">
                <span class="detail-label"><?php _e('UTR Number:', 'iplpro'); ?></span>
                <span class="detail-value"><?php echo esc_html($utr_number); ?></span>
            </div>
        </div>
        
        <div class="ticket-details">
            <h3 class="details-title"><?php _e('Match Details', 'iplpro'); ?></h3>
            
            <div class="match-teams">
                <span class="team-home"><?php echo esc_html($home_team); ?></span>
                <span class="vs">vs</span>
                <span class="team-away"><?php echo esc_html($away_team); ?></span>
            </div>
            
            <div class="match-info">
                <div class="match-date">
                    <span class="info-label"><?php _e('Date & Time:', 'iplpro'); ?></span>
                    <span class="info-value"><?php echo esc_html(iplpro_format_match_date($match_date)); ?></span>
                </div>
                
                <div class="match-venue">
                    <span class="info-label"><?php _e('Venue:', 'iplpro'); ?></span>
                    <span class="info-value"><?php echo esc_html($stadium); ?></span>
                </div>
                
                <div class="ticket-type-info">
                    <span class="info-label"><?php _e('Ticket Type:', 'iplpro'); ?></span>
                    <span class="info-value"><?php echo esc_html($ticket_type); ?></span>
                </div>
                
                <div class="ticket-quantity">
                    <span class="info-label"><?php _e('Quantity:', 'iplpro'); ?></span>
                    <span class="info-value"><?php echo esc_html($quantity); ?> <?php echo _n('ticket', 'tickets', $quantity, 'iplpro'); ?></span>
                </div>
                
                <div class="ticket-price">
                    <span class="info-label"><?php _e('Price:', 'iplpro'); ?></span>
                    <span class="info-value">₹<?php echo esc_html($unit_price); ?> <?php _e('per ticket', 'iplpro'); ?></span>
                </div>
                
                <div class="order-total">
                    <span class="info-label"><?php _e('Total Amount:', 'iplpro'); ?></span>
                    <span class="info-value">₹<?php echo esc_html($total_amount); ?></span>
                </div>
            </div>
        </div>
        
        <div class="customer-details">
            <h3 class="details-title"><?php _e('Customer Details', 'iplpro'); ?></h3>
            
            <div class="customer-info">
                <div class="customer-name">
                    <span class="info-label"><?php _e('Name:', 'iplpro'); ?></span>
                    <span class="info-value"><?php echo esc_html($customer_name); ?></span>
                </div>
                
                <div class="customer-email">
                    <span class="info-label"><?php _e('Email:', 'iplpro'); ?></span>
                    <span class="info-value"><?php echo esc_html($customer_email); ?></span>
                </div>
            </div>
        </div>
        
        <div class="confirmation-message">
            <p><?php _e('Your payment is being verified. Tickets will be sent to your email once verification is complete.', 'iplpro'); ?></p>
            <p><?php _e('For any issues or inquiries, please contact our support team.', 'iplpro'); ?></p>
        </div>
        
        <div class="confirmation-actions">
            <a href="<?php echo esc_url(home_url()); ?>" class="home-btn"><?php _e('Return to Home', 'iplpro'); ?></a>
        </div>
    </div>
    <?php
}