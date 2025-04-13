<?php
/**
 * Advanced Custom Fields configuration
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if ACF is installed and active
if (!function_exists('acf_add_local_field_group')) {
    // Add a notice in admin if ACF is not active
    function iplpro_acf_admin_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('IPL Pro theme requires Advanced Custom Fields Pro to be installed and activated.', 'iplpro'); ?></p>
        </div>
        <?php
    }
    add_action('admin_notices', 'iplpro_acf_admin_notice');
    return;
}

/**
 * Register ACF fields for Match post type
 */
function iplpro_register_acf_fields() {
    
    // Match Details Field Group
    acf_add_local_field_group(array(
        'key' => 'group_match_details',
        'title' => 'Match Details',
        'fields' => array(
            array(
                'key' => 'field_match_date',
                'label' => 'Match Date',
                'name' => 'match_date',
                'type' => 'date_time_picker',
                'required' => 1,
                'display_format' => 'd/m/Y g:i a',
                'return_format' => 'd/m/Y g:i a',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_home_team',
                'label' => 'Home Team',
                'name' => 'home_team',
                'type' => 'taxonomy',
                'taxonomy' => 'team',
                'field_type' => 'select',
                'return_format' => 'id',
                'required' => 1,
            ),
            array(
                'key' => 'field_away_team',
                'label' => 'Away Team',
                'name' => 'away_team',
                'type' => 'taxonomy',
                'taxonomy' => 'team',
                'field_type' => 'select',
                'return_format' => 'id',
                'required' => 1,
            ),
            array(
                'key' => 'field_stadium',
                'label' => 'Stadium',
                'name' => 'stadium',
                'type' => 'taxonomy',
                'taxonomy' => 'stadium',
                'field_type' => 'select',
                'return_format' => 'id',
                'required' => 1,
            ),
            array(
                'key' => 'field_match_status',
                'label' => 'Match Status',
                'name' => 'match_status',
                'type' => 'select',
                'choices' => array(
                    'upcoming' => 'Upcoming',
                    'live' => 'Live',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ),
                'default_value' => 'upcoming',
                'return_format' => 'value',
                'required' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'match',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ));
    
    // Ticket Categories Field Group
    acf_add_local_field_group(array(
        'key' => 'group_ticket_categories',
        'title' => 'Ticket Categories',
        'fields' => array(
            array(
                'key' => 'field_ticket_categories',
                'label' => 'Ticket Categories',
                'name' => 'ticket_categories',
                'type' => 'repeater',
                'required' => 1,
                'min' => 1,
                'max' => 10,
                'layout' => 'block',
                'button_label' => 'Add Ticket Category',
                'sub_fields' => array(
                    array(
                        'key' => 'field_ticket_type',
                        'label' => 'Ticket Type',
                        'name' => 'ticket_type',
                        'type' => 'text',
                        'required' => 1,
                        'placeholder' => 'e.g. Premium Stand',
                    ),
                    array(
                        'key' => 'field_ticket_description',
                        'label' => 'Description',
                        'name' => 'ticket_description',
                        'type' => 'textarea',
                        'rows' => 3,
                        'placeholder' => 'e.g. Premium seating with excellent view',
                    ),
                    array(
                        'key' => 'field_ticket_price',
                        'label' => 'Price (₹)',
                        'name' => 'ticket_price',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'default_value' => 999,
                    ),
                    array(
                        'key' => 'field_seats_available',
                        'label' => 'Seats Available',
                        'name' => 'seats_available',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'default_value' => 100,
                    ),
                    array(
                        'key' => 'field_section_color',
                        'label' => 'Section Color (HEX)',
                        'name' => 'section_color',
                        'type' => 'color_picker',
                        'default_value' => '#e1bee7',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'match',
                ),
            ),
        ),
        'menu_order' => 10,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ));
    
    // Payment Options Field Group
    acf_add_local_field_group(array(
        'key' => 'group_payment_options',
        'title' => 'Payment Options',
        'fields' => array(
            array(
                'key' => 'field_enable_upi',
                'label' => 'Enable UPI Payment',
                'name' => 'enable_upi',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_upi_id',
                'label' => 'UPI ID',
                'name' => 'upi_id',
                'type' => 'text',
                'required' => 1,
                'default_value' => 'ipl@upi',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_enable_upi',
                            'operator' => '==',
                            'value' => 1,
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_qr_code',
                'label' => 'QR Code',
                'name' => 'qr_code',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'uploadedTo',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_enable_upi',
                            'operator' => '==',
                            'value' => 1,
                        ),
                    ),
                ),
            ),
            array(
                'key' => 'field_enable_cards',
                'label' => 'Enable Card Payments',
                'name' => 'enable_cards',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_enable_wallet',
                'label' => 'Enable Wallet Payments',
                'name' => 'enable_wallet',
                'type' => 'true_false',
                'default_value' => 1,
                'ui' => 1,
            ),
            array(
                'key' => 'field_service_fee',
                'label' => 'Service Fee (₹)',
                'name' => 'service_fee',
                'type' => 'number',
                'default_value' => 75,
                'min' => 0,
            ),
            array(
                'key' => 'field_gst_percentage',
                'label' => 'GST Percentage (%)',
                'name' => 'gst_percentage',
                'type' => 'number',
                'default_value' => 18,
                'min' => 0,
                'max' => 100,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-payment-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ));
    
    // Team Details Field Group
    acf_add_local_field_group(array(
        'key' => 'group_team_details',
        'title' => 'Team Details',
        'fields' => array(
            array(
                'key' => 'field_team_logo',
                'label' => 'Team Logo',
                'name' => 'team_logo',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'thumbnail',
                'library' => 'all',
            ),
            array(
                'key' => 'field_team_short_name',
                'label' => 'Short Name',
                'name' => 'team_short_name',
                'type' => 'text',
                'instructions' => 'Short abbreviation for the team (e.g. CSK, MI)',
                'maxlength' => 5,
            ),
            array(
                'key' => 'field_team_primary_color',
                'label' => 'Primary Color',
                'name' => 'team_primary_color',
                'type' => 'color_picker',
                'default_value' => '#ff4e00',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'team',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ));
    
    // Stadium Details Field Group
    acf_add_local_field_group(array(
        'key' => 'group_stadium_details',
        'title' => 'Stadium Details',
        'fields' => array(
            array(
                'key' => 'field_stadium_image',
                'label' => 'Stadium Image',
                'name' => 'stadium_image',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            array(
                'key' => 'field_seating_capacity',
                'label' => 'Seating Capacity',
                'name' => 'seating_capacity',
                'type' => 'number',
                'min' => 0,
            ),
            array(
                'key' => 'field_location',
                'label' => 'Location',
                'name' => 'location',
                'type' => 'text',
            ),
            array(
                'key' => 'field_google_map_link',
                'label' => 'Google Map Link',
                'name' => 'google_map_link',
                'type' => 'url',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'taxonomy',
                    'operator' => '==',
                    'value' => 'stadium',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ));
    
    // Ticket Order Field Group
    acf_add_local_field_group(array(
        'key' => 'group_ticket_order',
        'title' => 'Ticket Order Details',
        'fields' => array(
            array(
                'key' => 'field_customer_details',
                'label' => 'Customer Details',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_customer_name',
                'label' => 'Customer Name',
                'name' => '_customer_name',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_customer_email',
                'label' => 'Email',
                'name' => '_customer_email',
                'type' => 'email',
                'required' => 1,
            ),
            array(
                'key' => 'field_customer_phone',
                'label' => 'Phone',
                'name' => '_customer_phone',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_order_details',
                'label' => 'Order Details',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_match_id',
                'label' => 'Match',
                'name' => '_match_id',
                'type' => 'post_object',
                'post_type' => array('match'),
                'return_format' => 'id',
                'required' => 1,
            ),
            array(
                'key' => 'field_ticket_type_order',
                'label' => 'Ticket Type',
                'name' => '_ticket_type',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_quantity_order',
                'label' => 'Quantity',
                'name' => '_quantity',
                'type' => 'number',
                'required' => 1,
                'min' => 1,
            ),
            array(
                'key' => 'field_unit_price',
                'label' => 'Unit Price (₹)',
                'name' => '_unit_price',
                'type' => 'number',
                'required' => 1,
            ),
            array(
                'key' => 'field_base_amount',
                'label' => 'Base Amount (₹)',
                'name' => '_base_amount',
                'type' => 'number',
                'required' => 1,
            ),
            array(
                'key' => 'field_gst_amount',
                'label' => 'GST Amount (₹)',
                'name' => '_gst_amount',
                'type' => 'number',
                'required' => 1,
            ),
            array(
                'key' => 'field_service_fee_amount',
                'label' => 'Service Fee (₹)',
                'name' => '_service_fee',
                'type' => 'number',
                'required' => 1,
            ),
            array(
                'key' => 'field_total_amount',
                'label' => 'Total Amount (₹)',
                'name' => '_total_amount',
                'type' => 'number',
                'required' => 1,
            ),
            array(
                'key' => 'field_payment_details',
                'label' => 'Payment Details',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_payment_method',
                'label' => 'Payment Method',
                'name' => '_payment_method',
                'type' => 'select',
                'choices' => array(
                    'upi' => 'UPI',
                    'card' => 'Credit/Debit Card',
                    'wallet' => 'Wallet',
                    'netbanking' => 'Net Banking',
                ),
                'required' => 1,
            ),
            array(
                'key' => 'field_utr_number',
                'label' => 'UTR Number',
                'name' => '_utr_number',
                'type' => 'text',
                'instructions' => 'UTR (Unique Transaction Reference) number provided by customer after payment',
            ),
            array(
                'key' => 'field_payment_date',
                'label' => 'Payment Date',
                'name' => '_payment_date',
                'type' => 'date_time_picker',
                'display_format' => 'd/m/Y g:i a',
                'return_format' => 'Y-m-d H:i:s',
            ),
            array(
                'key' => 'field_payment_verified',
                'label' => 'Payment Verified',
                'name' => '_payment_verified',
                'type' => 'true_false',
                'default_value' => 0,
                'ui' => 1,
                'ui_on_text' => 'Yes',
                'ui_off_text' => 'No',
            ),
            array(
                'key' => 'field_admin_notes',
                'label' => 'Admin Notes',
                'name' => '_admin_notes',
                'type' => 'textarea',
                'rows' => 4,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'ticket_order',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'active' => true,
    ));
}
add_action('acf/init', 'iplpro_register_acf_fields');