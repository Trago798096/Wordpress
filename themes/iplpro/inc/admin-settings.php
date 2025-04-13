<?php
/**
 * Admin Settings for IPL Pro Theme
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register theme admin pages
 */
function iplpro_add_admin_pages() {
    // Add payment settings page
    add_menu_page(
        __('IPL Pro Settings', 'iplpro'),
        __('IPL Pro', 'iplpro'),
        'manage_options',
        'iplpro-settings',
        'iplpro_settings_page_callback',
        'dashicons-tickets-alt',
        30
    );
    
    // Add theme settings page
    add_submenu_page(
        'iplpro-settings',
        __('Theme Settings', 'iplpro'),
        __('Theme Settings', 'iplpro'),
        'manage_options',
        'iplpro-settings',
        'iplpro_settings_page_callback'
    );
    
    // Add payment settings page
    add_submenu_page(
        'iplpro-settings',
        __('Payment Settings', 'iplpro'),
        __('Payment Settings', 'iplpro'),
        'manage_options',
        'theme-payment-settings',
        'iplpro_payment_settings_page_callback'
    );
    
    // Add ticket management page
    add_submenu_page(
        'iplpro-settings',
        __('Ticket Management', 'iplpro'),
        __('Ticket Management', 'iplpro'),
        'manage_options',
        'iplpro-ticket-management',
        'iplpro_ticket_management_page_callback'
    );
    
    // Add help page
    add_submenu_page(
        'iplpro-settings',
        __('Help & Documentation', 'iplpro'),
        __('Help & Documentation', 'iplpro'),
        'manage_options',
        'iplpro-help',
        'iplpro_help_page_callback'
    );
}
add_action('admin_menu', 'iplpro_add_admin_pages');

/**
 * Register options page for ACF fields
 */
function iplpro_acf_options_page() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => __('Theme General Settings', 'iplpro'),
            'menu_title' => __('Theme Settings', 'iplpro'),
            'menu_slug'  => 'theme-general-settings',
            'capability' => 'manage_options',
            'redirect'   => false,
            'parent_slug' => 'iplpro-settings',
        ));
        
        acf_add_options_page(array(
            'page_title' => __('Payment Settings', 'iplpro'),
            'menu_title' => __('Payment Settings', 'iplpro'),
            'menu_slug'  => 'theme-payment-settings',
            'capability' => 'manage_options',
            'redirect'   => false,
            'parent_slug' => 'iplpro-settings',
        ));
    }
}
add_action('acf/init', 'iplpro_acf_options_page');

/**
 * Settings page callback
 */
function iplpro_settings_page_callback() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="iplpro-admin-section">
            <h2><?php _e('Theme Settings', 'iplpro'); ?></h2>
            <p><?php _e('Configure the overall appearance and behavior of your IPL Pro ticket booking site.', 'iplpro'); ?></p>
            
            <?php if (function_exists('acf_form')) : ?>
                <?php acf_form(array(
                    'post_id' => 'options',
                    'field_groups' => array('group_theme_settings'),
                    'submit_value' => __('Update Settings', 'iplpro'),
                )); ?>
            <?php else : ?>
                <div class="notice notice-warning">
                    <p><?php _e('Advanced Custom Fields Pro is required for these settings.', 'iplpro'); ?></p>
                </div>
                
                <p>
                    <a href="<?php echo admin_url('themes.php?page=tgmpa-install-plugins'); ?>" class="button button-primary">
                        <?php _e('Install Required Plugins', 'iplpro'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
        
        <div class="iplpro-admin-section">
            <h2><?php _e('Theme Customization', 'iplpro'); ?></h2>
            <p><?php _e('Customize the colors, fonts, and other visual aspects of your site.', 'iplpro'); ?></p>
            
            <p>
                <a href="<?php echo admin_url('customize.php'); ?>" class="button button-primary">
                    <?php _e('Customize Theme', 'iplpro'); ?>
                </a>
            </p>
        </div>
    </div>
    <style>
        .iplpro-admin-section {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin-top: 20px;
        }
        .iplpro-admin-section h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
    </style>
    <?php
}

/**
 * Payment settings page callback
 */
function iplpro_payment_settings_page_callback() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="iplpro-admin-section">
            <h2><?php _e('Payment Settings', 'iplpro'); ?></h2>
            <p><?php _e('Configure UPI payment options, service fees, and GST settings.', 'iplpro'); ?></p>
            
            <?php if (function_exists('acf_form')) : ?>
                <?php acf_form(array(
                    'post_id' => 'options',
                    'field_groups' => array('group_payment_options'),
                    'submit_value' => __('Update Payment Settings', 'iplpro'),
                )); ?>
            <?php else : ?>
                <div class="notice notice-warning">
                    <p><?php _e('Advanced Custom Fields Pro is required for these settings.', 'iplpro'); ?></p>
                </div>
                
                <p>
                    <a href="<?php echo admin_url('themes.php?page=tgmpa-install-plugins'); ?>" class="button button-primary">
                        <?php _e('Install Required Plugins', 'iplpro'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
        
        <div class="iplpro-admin-section">
            <h2><?php _e('UTR Verification', 'iplpro'); ?></h2>
            <p><?php _e('View and manage ticket orders that need UTR verification.', 'iplpro'); ?></p>
            
            <?php
            // Get pending payment orders
            $pending_orders = new WP_Query(array(
                'post_type' => 'ticket_order',
                'post_status' => 'pending-payment',
                'posts_per_page' => 5,
                'meta_query' => array(
                    array(
                        'key' => '_utr_number',
                        'compare' => 'EXISTS',
                    ),
                ),
            ));
            
            if ($pending_orders->have_posts()) :
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Order ID', 'iplpro'); ?></th>
                            <th><?php _e('Customer', 'iplpro'); ?></th>
                            <th><?php _e('Amount', 'iplpro'); ?></th>
                            <th><?php _e('UTR Number', 'iplpro'); ?></th>
                            <th><?php _e('Actions', 'iplpro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pending_orders->have_posts()) : $pending_orders->the_post(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo admin_url('post.php?post=' . get_the_ID() . '&action=edit'); ?>">
                                        <?php echo 'IPL-' . get_the_ID(); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo esc_html(get_post_meta(get_the_ID(), '_customer_name', true)); ?>
                                </td>
                                <td>
                                    ₹<?php echo esc_html(get_post_meta(get_the_ID(), '_total_amount', true)); ?>
                                </td>
                                <td>
                                    <?php echo esc_html(get_post_meta(get_the_ID(), '_utr_number', true)); ?>
                                </td>
                                <td>
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?action=iplpro_verify_payment&order_id=' . get_the_ID()), 'iplpro_verify_payment'); ?>" class="button button-small">
                                        <?php _e('Verify', 'iplpro'); ?>
                                    </a>
                                    <a href="<?php echo admin_url('post.php?post=' . get_the_ID() . '&action=edit'); ?>" class="button button-small">
                                        <?php _e('View', 'iplpro'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </tbody>
                </table>
                
                <p>
                    <a href="<?php echo admin_url('edit.php?post_type=ticket_order&post_status=pending-payment'); ?>" class="button">
                        <?php _e('View All Pending Orders', 'iplpro'); ?>
                    </a>
                </p>
            <?php else : ?>
                <p><?php _e('No pending UTR verifications found.', 'iplpro'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Ticket management page callback
 */
function iplpro_ticket_management_page_callback() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // Handle seat availability update
    if (isset($_POST['iplpro_update_seats']) && isset($_POST['match_id']) && isset($_POST['ticket_type']) && isset($_POST['seats_available'])) {
        // Verify nonce
        if (isset($_POST['iplpro_ticket_nonce']) && wp_verify_nonce($_POST['iplpro_ticket_nonce'], 'iplpro_update_seats')) {
            $match_id = intval($_POST['match_id']);
            $ticket_type = sanitize_text_field($_POST['ticket_type']);
            $seats = intval($_POST['seats_available']);
            
            // Get ticket categories
            $ticket_categories = get_field('ticket_categories', $match_id);
            
            if ($ticket_categories && is_array($ticket_categories)) {
                foreach ($ticket_categories as $key => $category) {
                    if ($category['ticket_type'] === $ticket_type) {
                        // Update seats available
                        $ticket_categories[$key]['seats_available'] = $seats;
                        
                        // Save updated field
                        update_field('ticket_categories', $ticket_categories, $match_id);
                        
                        // Show success message
                        add_settings_error(
                            'iplpro_ticket_management',
                            'seats_updated',
                            __('Seats availability updated successfully.', 'iplpro'),
                            'updated'
                        );
                        
                        break;
                    }
                }
            }
        }
    }
    
    // Get upcoming matches
    $upcoming_matches = new WP_Query(array(
        'post_type' => 'match',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'match_status',
                'value' => 'upcoming',
                'compare' => '=',
            ),
        ),
        'orderby' => 'meta_value',
        'meta_key' => 'match_date',
        'order' => 'ASC',
    ));
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <?php settings_errors('iplpro_ticket_management'); ?>
        
        <div class="iplpro-admin-section">
            <h2><?php _e('Upcoming Matches', 'iplpro'); ?></h2>
            
            <?php if ($upcoming_matches->have_posts()) : ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Match', 'iplpro'); ?></th>
                            <th><?php _e('Date', 'iplpro'); ?></th>
                            <th><?php _e('Venue', 'iplpro'); ?></th>
                            <th><?php _e('Actions', 'iplpro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($upcoming_matches->have_posts()) : $upcoming_matches->the_post(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo admin_url('post.php?post=' . get_the_ID() . '&action=edit'); ?>">
                                        <?php 
                                        $home_team = iplpro_get_term_name(get_field('home_team'));
                                        $away_team = iplpro_get_term_name(get_field('away_team'));
                                        echo esc_html($home_team . ' vs ' . $away_team); 
                                        ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo esc_html(get_field('match_date')); ?>
                                </td>
                                <td>
                                    <?php echo esc_html(iplpro_get_term_name(get_field('stadium'), 'Stadium')); ?>
                                </td>
                                <td>
                                    <a href="#" class="button button-small show-ticket-categories" data-match="<?php echo get_the_ID(); ?>">
                                        <?php _e('Manage Tickets', 'iplpro'); ?>
                                    </a>
                                    <a href="<?php echo admin_url('post.php?post=' . get_the_ID() . '&action=edit'); ?>" class="button button-small">
                                        <?php _e('Edit Match', 'iplpro'); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr class="ticket-categories-row" id="ticket-categories-<?php echo get_the_ID(); ?>" style="display: none;">
                                <td colspan="4">
                                    <div class="ticket-categories-container">
                                        <h3><?php _e('Ticket Categories', 'iplpro'); ?></h3>
                                        
                                        <?php
                                        $ticket_categories = get_field('ticket_categories');
                                        
                                        if ($ticket_categories && is_array($ticket_categories)) :
                                            foreach ($ticket_categories as $category) :
                                                ?>
                                                <div class="ticket-category-box">
                                                    <h4><?php echo esc_html($category['ticket_type']); ?></h4>
                                                    <p><strong><?php _e('Price:', 'iplpro'); ?></strong> ₹<?php echo esc_html($category['ticket_price']); ?></p>
                                                    <p><strong><?php _e('Description:', 'iplpro'); ?></strong> <?php echo esc_html($category['ticket_description']); ?></p>
                                                    
                                                    <form method="post" class="seats-update-form">
                                                        <?php wp_nonce_field('iplpro_update_seats', 'iplpro_ticket_nonce'); ?>
                                                        <input type="hidden" name="match_id" value="<?php echo get_the_ID(); ?>">
                                                        <input type="hidden" name="ticket_type" value="<?php echo esc_attr($category['ticket_type']); ?>">
                                                        
                                                        <div class="seats-field">
                                                            <label for="seats-<?php echo esc_attr($category['ticket_type']); ?>">
                                                                <strong><?php _e('Seats Available:', 'iplpro'); ?></strong>
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="seats-<?php echo esc_attr($category['ticket_type']); ?>"
                                                                name="seats_available"
                                                                value="<?php echo esc_attr($category['seats_available']); ?>"
                                                                min="0"
                                                            >
                                                            <button type="submit" name="iplpro_update_seats" class="button button-small">
                                                                <?php _e('Update', 'iplpro'); ?>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <?php
                                            endforeach;
                                        else:
                                            ?>
                                            <p><?php _e('No ticket categories defined for this match.', 'iplpro'); ?></p>
                                            <?php
                                        endif;
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </tbody>
                </table>
                
                <p>
                    <a href="<?php echo admin_url('post-new.php?post_type=match'); ?>" class="button button-primary">
                        <?php _e('Add New Match', 'iplpro'); ?>
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=match'); ?>" class="button">
                        <?php _e('View All Matches', 'iplpro'); ?>
                    </a>
                </p>
            <?php else : ?>
                <p><?php _e('No upcoming matches found.', 'iplpro'); ?></p>
                
                <p>
                    <a href="<?php echo admin_url('post-new.php?post_type=match'); ?>" class="button button-primary">
                        <?php _e('Add New Match', 'iplpro'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        .iplpro-admin-section {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin-top: 20px;
        }
        .iplpro-admin-section h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .ticket-categories-container {
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 3px;
        }
        .ticket-category-box {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .ticket-category-box:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .ticket-category-box h4 {
            margin-top: 0;
        }
        .seats-field {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .seats-field input {
            width: 80px;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        $('.show-ticket-categories').on('click', function(e) {
            e.preventDefault();
            const matchId = $(this).data('match');
            $('#ticket-categories-' + matchId).toggle();
        });
    });
    </script>
    <?php
}

/**
 * Help page callback
 */
function iplpro_help_page_callback() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="iplpro-admin-section">
            <h2><?php _e('Quick Start Guide', 'iplpro'); ?></h2>
            
            <div class="iplpro-help-tabs">
                <ul class="iplpro-tab-nav">
                    <li class="active"><a href="#tab-setup"><?php _e('Initial Setup', 'iplpro'); ?></a></li>
                    <li><a href="#tab-matches"><?php _e('Managing Matches', 'iplpro'); ?></a></li>
                    <li><a href="#tab-tickets"><?php _e('Ticket Categories', 'iplpro'); ?></a></li>
                    <li><a href="#tab-payments"><?php _e('Payment System', 'iplpro'); ?></a></li>
                    <li><a href="#tab-orders"><?php _e('Order Management', 'iplpro'); ?></a></li>
                </ul>
                
                <div class="iplpro-tab-content">
                    <div id="tab-setup" class="iplpro-tab-pane active">
                        <h3><?php _e('Setting Up Your IPL Ticket Booking Site', 'iplpro'); ?></h3>
                        
                        <ol>
                            <li>
                                <strong><?php _e('Install Required Plugins', 'iplpro'); ?></strong>
                                <p><?php _e('The IPL Pro theme requires Advanced Custom Fields Pro to be installed and activated.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Configure Payment Settings', 'iplpro'); ?></strong>
                                <p><?php _e('Set up your UPI ID and payment options in the Payment Settings page.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Customize Theme Appearance', 'iplpro'); ?></strong>
                                <p><?php _e('Use the WordPress Customizer to adjust colors, logos, and other visual elements.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Add Teams and Stadiums', 'iplpro'); ?></strong>
                                <p><?php _e('Before adding matches, make sure to add teams and stadiums in their respective taxonomies.', 'iplpro'); ?></p>
                            </li>
                        </ol>
                    </div>
                    
                    <div id="tab-matches" class="iplpro-tab-pane">
                        <h3><?php _e('Creating and Managing Matches', 'iplpro'); ?></h3>
                        
                        <ol>
                            <li>
                                <strong><?php _e('Create a New Match', 'iplpro'); ?></strong>
                                <p><?php _e('Go to Matches → Add New and fill in the match details:', 'iplpro'); ?></p>
                                <ul>
                                    <li><?php _e('Match title (e.g., "Match 1: CSK vs MI")', 'iplpro'); ?></li>
                                    <li><?php _e('Match date and time', 'iplpro'); ?></li>
                                    <li><?php _e('Home and away teams', 'iplpro'); ?></li>
                                    <li><?php _e('Stadium/venue', 'iplpro'); ?></li>
                                    <li><?php _e('Match status (upcoming, live, completed, cancelled)', 'iplpro'); ?></li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php _e('Add Ticket Categories', 'iplpro'); ?></strong>
                                <p><?php _e('For each match, define the available ticket categories with prices and seat counts.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Feature Image', 'iplpro'); ?></strong>
                                <p><?php _e('Add a featured image that will be displayed on the match listing page.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Managing Seat Availability', 'iplpro'); ?></strong>
                                <p><?php _e('You can update seat availability from the Ticket Management page or directly in the match editor.', 'iplpro'); ?></p>
                            </li>
                        </ol>
                    </div>
                    
                    <div id="tab-tickets" class="iplpro-tab-pane">
                        <h3><?php _e('Setting Up Ticket Categories', 'iplpro'); ?></h3>
                        
                        <ol>
                            <li>
                                <strong><?php _e('Define Ticket Types', 'iplpro'); ?></strong>
                                <p><?php _e('For each match, you can add multiple ticket categories such as:', 'iplpro'); ?></p>
                                <ul>
                                    <li><?php _e('Premium Stand', 'iplpro'); ?></li>
                                    <li><?php _e('VIP Box', 'iplpro'); ?></li>
                                    <li><?php _e('General Stand', 'iplpro'); ?></li>
                                    <li><?php _e('Corporate Box', 'iplpro'); ?></li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php _e('Set Prices and Seat Counts', 'iplpro'); ?></strong>
                                <p><?php _e('For each ticket category, set the price and number of available seats.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Add Description', 'iplpro'); ?></strong>
                                <p><?php _e('Include a description for each ticket type to help customers understand what they\'re purchasing.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Assign Colors', 'iplpro'); ?></strong>
                                <p><?php _e('Choose a color for each section to be displayed on the stadium map.', 'iplpro'); ?></p>
                            </li>
                        </ol>
                    </div>
                    
                    <div id="tab-payments" class="iplpro-tab-pane">
                        <h3><?php _e('Payment System Setup and Verification', 'iplpro'); ?></h3>
                        
                        <ol>
                            <li>
                                <strong><?php _e('Configure UPI Settings', 'iplpro'); ?></strong>
                                <p><?php _e('In Payment Settings, set your UPI ID that will be used for receiving payments.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Upload QR Code', 'iplpro'); ?></strong>
                                <p><?php _e('Upload a QR code image linked to your UPI ID for payment scanning.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Set Service Fee and GST', 'iplpro'); ?></strong>
                                <p><?php _e('Configure the service fee amount and GST percentage that will be applied to all bookings.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('UTR Verification Process', 'iplpro'); ?></strong>
                                <p><?php _e('When customers make a payment, they submit the UTR (Unique Transaction Reference) number. You\'ll need to verify these payments in the admin panel.', 'iplpro'); ?></p>
                                <ul>
                                    <li><?php _e('Go to Ticket Orders → Pending Payment', 'iplpro'); ?></li>
                                    <li><?php _e('Check the UTR number against your payment gateway or bank statement', 'iplpro'); ?></li>
                                    <li><?php _e('Click "Verify Payment" if the payment is confirmed', 'iplpro'); ?></li>
                                </ul>
                            </li>
                        </ol>
                    </div>
                    
                    <div id="tab-orders" class="iplpro-tab-pane">
                        <h3><?php _e('Managing Ticket Orders', 'iplpro'); ?></h3>
                        
                        <ol>
                            <li>
                                <strong><?php _e('Order Statuses', 'iplpro'); ?></strong>
                                <p><?php _e('Ticket orders can have the following statuses:', 'iplpro'); ?></p>
                                <ul>
                                    <li><?php _e('Pending Payment: UTR submitted but not yet verified', 'iplpro'); ?></li>
                                    <li><?php _e('Payment Verified: Payment confirmed but ticket not yet issued', 'iplpro'); ?></li>
                                    <li><?php _e('Ticket Issued: Order completed with ticket issued to customer', 'iplpro'); ?></li>
                                </ul>
                            </li>
                            <li>
                                <strong><?php _e('Verifying Payments', 'iplpro'); ?></strong>
                                <p><?php _e('In the Ticket Order edit screen, you\'ll see a Payment Verification meta box where you can verify the UTR payment.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Issuing Tickets', 'iplpro'); ?></strong>
                                <p><?php _e('After verifying payment, change the order status to "Ticket Issued" to trigger the ticket email to the customer.', 'iplpro'); ?></p>
                            </li>
                            <li>
                                <strong><?php _e('Order Reports', 'iplpro'); ?></strong>
                                <p><?php _e('View order reports and statistics in the Ticket Orders section.', 'iplpro'); ?></p>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .iplpro-admin-section {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin-top: 20px;
        }
        .iplpro-admin-section h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .iplpro-help-tabs {
            margin-top: 20px;
        }
        .iplpro-tab-nav {
            display: flex;
            border-bottom: 1px solid #ccc;
            margin: 0;
            padding: 0;
        }
        .iplpro-tab-nav li {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .iplpro-tab-nav li a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: #444;
            font-weight: 500;
            border: 1px solid transparent;
            border-bottom: none;
            margin-bottom: -1px;
        }
        .iplpro-tab-nav li.active a {
            border-color: #ccc;
            border-bottom-color: #fff;
            background: #fff;
        }
        .iplpro-tab-content {
            padding: 20px 0;
        }
        .iplpro-tab-pane {
            display: none;
        }
        .iplpro-tab-pane.active {
            display: block;
        }
        .iplpro-tab-pane h3 {
            margin-top: 0;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        $('.iplpro-tab-nav a').on('click', function(e) {
            e.preventDefault();
            const target = $(this).attr('href');
            
            $('.iplpro-tab-nav li').removeClass('active');
            $(this).parent('li').addClass('active');
            
            $('.iplpro-tab-pane').removeClass('active');
            $(target).addClass('active');
        });
    });
    </script>
    <?php
}

/**
 * Add UTR verification to order actions
 */
function iplpro_ticket_order_actions($actions, $post) {
    if ($post->post_type !== 'ticket_order') {
        return $actions;
    }
    
    // Check if order has UTR and is pending verification
    $utr = get_post_meta($post->ID, '_utr_number', true);
    $verified = get_post_meta($post->ID, '_payment_verified', true);
    
    if ($utr && $post->post_status === 'pending-payment' && !$verified) {
        $verify_url = wp_nonce_url(admin_url('admin.php?action=iplpro_verify_payment&order_id=' . $post->ID), 'iplpro_verify_payment');
        $actions['verify_payment'] = '<a href="' . esc_url($verify_url) . '" class="verify-payment" style="color: #46b450;">' . __('Verify Payment', 'iplpro') . '</a>';
    }
    
    return $actions;
}
add_filter('post_row_actions', 'iplpro_ticket_order_actions', 10, 2);

/**
 * Add dashboard widgets
 */
function iplpro_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'iplpro_dashboard_widget',
        __('IPL Pro Ticket Booking', 'iplpro'),
        'iplpro_dashboard_widget_callback'
    );
}
add_action('wp_dashboard_setup', 'iplpro_add_dashboard_widgets');

/**
 * Dashboard widget callback
 */
function iplpro_dashboard_widget_callback() {
    // Count upcoming matches
    $upcoming_matches = new WP_Query(array(
        'post_type' => 'match',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'match_status',
                'value' => 'upcoming',
                'compare' => '=',
            ),
        ),
        'posts_per_page' => -1,
    ));
    
    // Count pending orders
    $pending_orders = new WP_Query(array(
        'post_type' => 'ticket_order',
        'post_status' => 'pending-payment',
        'posts_per_page' => -1,
    ));
    
    // Count verified orders
    $verified_orders = new WP_Query(array(
        'post_type' => 'ticket_order',
        'post_status' => 'payment-verified',
        'posts_per_page' => -1,
    ));
    
    // Total revenue
    $total_revenue = 0;
    
    $all_orders = new WP_Query(array(
        'post_type' => 'ticket_order',
        'post_status' => array('payment-verified', 'ticket-issued'),
        'posts_per_page' => -1,
    ));
    
    if ($all_orders->have_posts()) {
        while ($all_orders->have_posts()) {
            $all_orders->the_post();
            $total_revenue += floatval(get_post_meta(get_the_ID(), '_total_amount', true));
        }
    }
    
    wp_reset_postdata();
    
    ?>
    <div class="iplpro-dashboard-widget">
        <div class="iplpro-stat-grid">
            <div class="iplpro-stat-box">
                <div class="iplpro-stat-value"><?php echo $upcoming_matches->post_count; ?></div>
                <div class="iplpro-stat-label"><?php _e('Upcoming Matches', 'iplpro'); ?></div>
            </div>
            
            <div class="iplpro-stat-box">
                <div class="iplpro-stat-value"><?php echo $pending_orders->post_count; ?></div>
                <div class="iplpro-stat-label"><?php _e('Pending Orders', 'iplpro'); ?></div>
            </div>
            
            <div class="iplpro-stat-box">
                <div class="iplpro-stat-value"><?php echo $verified_orders->post_count; ?></div>
                <div class="iplpro-stat-label"><?php _e('Verified Orders', 'iplpro'); ?></div>
            </div>
            
            <div class="iplpro-stat-box">
                <div class="iplpro-stat-value">₹<?php echo number_format($total_revenue, 0, '.', ','); ?></div>
                <div class="iplpro-stat-label"><?php _e('Total Revenue', 'iplpro'); ?></div>
            </div>
        </div>
        
        <div class="iplpro-quick-links">
            <a href="<?php echo admin_url('post-new.php?post_type=match'); ?>" class="button">
                <?php _e('Add Match', 'iplpro'); ?>
            </a>
            
            <a href="<?php echo admin_url('edit.php?post_type=ticket_order&post_status=pending-payment'); ?>" class="button">
                <?php _e('Verify Payments', 'iplpro'); ?>
            </a>
            
            <a href="<?php echo admin_url('admin.php?page=iplpro-ticket-management'); ?>" class="button">
                <?php _e('Manage Tickets', 'iplpro'); ?>
            </a>
        </div>
    </div>
    
    <style>
        .iplpro-stat-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        .iplpro-stat-box {
            background: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
        }
        .iplpro-stat-value {
            font-size: 24px;
            font-weight: 600;
            color: #1a2a47;
        }
        .iplpro-stat-label {
            font-size: 12px;
            color: #777;
        }
        .iplpro-quick-links {
            display: flex;
            gap: 5px;
            justify-content: space-between;
        }
        .iplpro-quick-links .button {
            flex: 1;
            text-align: center;
        }
    </style>
    <?php
}

/**
 * Add theme settings to customizer
 */
function iplpro_customize_register($wp_customize) {
    // Add IPL Pro section
    $wp_customize->add_section('iplpro_settings', array(
        'title' => __('IPL Pro Theme Settings', 'iplpro'),
        'priority' => 30,
    ));
    
    // Logo upload setting
    $wp_customize->add_setting('iplpro_logo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'iplpro_logo', array(
        'label' => __('Site Logo', 'iplpro'),
        'section' => 'iplpro_settings',
        'settings' => 'iplpro_logo',
    )));
    
    // Primary color setting
    $wp_customize->add_setting('iplpro_primary_color', array(
        'default' => '#ff4e00',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'iplpro_primary_color', array(
        'label' => __('Primary Color', 'iplpro'),
        'section' => 'iplpro_settings',
        'settings' => 'iplpro_primary_color',
    )));
    
    // Secondary color setting
    $wp_customize->add_setting('iplpro_secondary_color', array(
        'default' => '#1a2a47',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'iplpro_secondary_color', array(
        'label' => __('Secondary Color', 'iplpro'),
        'section' => 'iplpro_settings',
        'settings' => 'iplpro_secondary_color',
    )));
    
    // Footer text setting
    $wp_customize->add_setting('iplpro_footer_text', array(
        'default' => __('© 2025 IPL Pro. All rights reserved.', 'iplpro'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('iplpro_footer_text', array(
        'label' => __('Footer Text', 'iplpro'),
        'section' => 'iplpro_settings',
        'type' => 'text',
    ));
}
add_action('customize_register', 'iplpro_customize_register');

/**
 * Output customizer CSS
 */
function iplpro_customizer_css() {
    $primary_color = get_theme_mod('iplpro_primary_color', '#ff4e00');
    $secondary_color = get_theme_mod('iplpro_secondary_color', '#1a2a47');
    
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --secondary-color: <?php echo $secondary_color; ?>;
        }
        
        .book-btn,
        .book-ticket-btn,
        .form-submit-btn,
        .match-header,
        .payment-tab.active {
            background-color: var(--primary-color);
        }
        
        a,
        .site-title span,
        .ticket-price,
        .venue-icon {
            color: var(--primary-color);
        }
        
        .site-footer,
        .logo-text,
        #site-navigation a {
            color: var(--secondary-color);
        }
        
        .site-footer {
            background-color: var(--secondary-color);
        }
    </style>
    <?php
}
add_action('wp_head', 'iplpro_customizer_css');

/**
 * Register team manager role
 */
function iplpro_register_roles() {
    add_role(
        'stadium_manager',
        __('Stadium Manager', 'iplpro'),
        array(
            'read' => true,
            'edit_posts' => true,
            'edit_match' => true,
            'edit_matches' => true,
            'edit_published_matches' => true,
            'manage_ticket_inventory' => true,
        )
    );
}
add_action('after_switch_theme', 'iplpro_register_roles');

/**
 * Add capabilities for stadium manager
 */
function iplpro_add_role_caps() {
    $role = get_role('stadium_manager');
    
    if (!$role) {
        return;
    }
    
    // Add match capabilities
    $role->add_cap('read');
    $role->add_cap('edit_match');
    $role->add_cap('edit_matches');
    $role->add_cap('edit_published_matches');
    $role->add_cap('manage_ticket_inventory');
}
add_action('admin_init', 'iplpro_add_role_caps');

/**
 * Filter stadium manager capabilities
 */
function iplpro_filter_stadium_manager_caps($caps, $cap, $user_id, $args) {
    // Only apply to stadium managers
    $user = get_userdata($user_id);
    
    if (!$user || !in_array('stadium_manager', $user->roles)) {
        return $caps;
    }
    
    // Allow editing matches but not creating new ones
    if ('edit_match' === $cap && isset($args[0])) {
        $post = get_post($args[0]);
        if ($post && 'match' === $post->post_type) {
            return array('edit_published_matches');
        }
    }
    
    return $caps;
}
add_filter('map_meta_cap', 'iplpro_filter_stadium_manager_caps', 10, 4);

/**
 * Customize admin menu for stadium manager
 */
function iplpro_stadium_manager_menu() {
    $user = wp_get_current_user();
    
    if (!in_array('stadium_manager', $user->roles)) {
        return;
    }
    
    // Remove unnecessary menu items
    remove_menu_page('index.php'); // Dashboard
    remove_menu_page('edit.php'); // Posts
    remove_menu_page('upload.php'); // Media
    remove_menu_page('edit.php?post_type=page'); // Pages
    remove_menu_page('edit-comments.php'); // Comments
    remove_menu_page('themes.php'); // Appearance
    remove_menu_page('plugins.php'); // Plugins
    remove_menu_page('users.php'); // Users
    remove_menu_page('tools.php'); // Tools
    remove_menu_page('options-general.php'); // Settings
    
    // Add custom dashboard as main page
    add_menu_page(
        __('Stadium Manager Dashboard', 'iplpro'),
        __('Dashboard', 'iplpro'),
        'manage_ticket_inventory',
        'stadium-manager-dashboard',
        'iplpro_stadium_manager_dashboard',
        'dashicons-tickets-alt',
        2
    );
}
add_action('admin_menu', 'iplpro_stadium_manager_menu', 999);

/**
 * Stadium manager dashboard
 */
function iplpro_stadium_manager_dashboard() {
    ?>
    <div class="wrap">
        <h1><?php _e('Stadium Manager Dashboard', 'iplpro'); ?></h1>
        
        <div class="iplpro-admin-section">
            <h2><?php _e('Upcoming Matches', 'iplpro'); ?></h2>
            
            <?php
            // Get upcoming matches
            $upcoming_matches = new WP_Query(array(
                'post_type' => 'match',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => 'match_status',
                        'value' => 'upcoming',
                        'compare' => '=',
                    ),
                ),
                'orderby' => 'meta_value',
                'meta_key' => 'match_date',
                'order' => 'ASC',
            ));
            
            if ($upcoming_matches->have_posts()) :
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e('Match', 'iplpro'); ?></th>
                            <th><?php _e('Date', 'iplpro'); ?></th>
                            <th><?php _e('Venue', 'iplpro'); ?></th>
                            <th><?php _e('Actions', 'iplpro'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($upcoming_matches->have_posts()) : $upcoming_matches->the_post(); ?>
                            <tr>
                                <td>
                                    <?php 
                                    $home_team = iplpro_get_term_name(get_field('home_team'));
                                    $away_team = iplpro_get_term_name(get_field('away_team'));
                                    echo esc_html($home_team . ' vs ' . $away_team); 
                                    ?>
                                </td>
                                <td>
                                    <?php echo esc_html(get_field('match_date')); ?>
                                </td>
                                <td>
                                    <?php echo esc_html(iplpro_get_term_name(get_field('stadium'), 'Stadium')); ?>
                                </td>
                                <td>
                                    <a href="#" class="button button-small show-ticket-categories" data-match="<?php echo get_the_ID(); ?>">
                                        <?php _e('Manage Tickets', 'iplpro'); ?>
                                    </a>
                                </td>
                            </tr>
                            <tr class="ticket-categories-row" id="ticket-categories-<?php echo get_the_ID(); ?>" style="display: none;">
                                <td colspan="4">
                                    <div class="ticket-categories-container">
                                        <h3><?php _e('Ticket Categories', 'iplpro'); ?></h3>
                                        
                                        <?php
                                        $ticket_categories = get_field('ticket_categories');
                                        
                                        if ($ticket_categories && is_array($ticket_categories)) :
                                            foreach ($ticket_categories as $category) :
                                                ?>
                                                <div class="ticket-category-box">
                                                    <h4><?php echo esc_html($category['ticket_type']); ?></h4>
                                                    <p><strong><?php _e('Price:', 'iplpro'); ?></strong> ₹<?php echo esc_html($category['ticket_price']); ?></p>
                                                    
                                                    <form method="post" class="seats-update-form">
                                                        <?php wp_nonce_field('iplpro_update_seats', 'iplpro_ticket_nonce'); ?>
                                                        <input type="hidden" name="match_id" value="<?php echo get_the_ID(); ?>">
                                                        <input type="hidden" name="ticket_type" value="<?php echo esc_attr($category['ticket_type']); ?>">
                                                        
                                                        <div class="seats-field">
                                                            <label for="seats-<?php echo esc_attr($category['ticket_type']); ?>">
                                                                <strong><?php _e('Seats Available:', 'iplpro'); ?></strong>
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="seats-<?php echo esc_attr($category['ticket_type']); ?>"
                                                                name="seats_available"
                                                                value="<?php echo esc_attr($category['seats_available']); ?>"
                                                                min="0"
                                                            >
                                                            <button type="submit" name="iplpro_update_seats" class="button button-small">
                                                                <?php _e('Update', 'iplpro'); ?>
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <?php
                                            endforeach;
                                        else:
                                            ?>
                                            <p><?php _e('No ticket categories defined for this match.', 'iplpro'); ?></p>
                                            <?php
                                        endif;
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('No upcoming matches found.', 'iplpro'); ?></p>
            <?php endif; ?>
        </div>
    </div>
    
    <style>
        .iplpro-admin-section {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin-top: 20px;
        }
        .iplpro-admin-section h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .ticket-categories-container {
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e5e5e5;
            border-radius: 3px;
        }
        .ticket-category-box {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .ticket-category-box:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .ticket-category-box h4 {
            margin-top: 0;
        }
        .seats-field {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .seats-field input {
            width: 80px;
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        $('.show-ticket-categories').on('click', function(e) {
            e.preventDefault();
            const matchId = $(this).data('match');
            $('#ticket-categories-' + matchId).toggle();
        });
    });
    </script>
    <?php
}