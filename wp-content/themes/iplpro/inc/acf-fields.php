<?php
/**
 * Advanced Custom Fields configuration for IPL Pro theme
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if ACF is active
if (!function_exists('acf_add_local_field_group')) {
    // Add admin notice if ACF is not active
    function iplpro_acf_admin_notice() {
        ?>
        <div class="notice notice-error">
            <p><?php _e('IPL Pro theme requires Advanced Custom Fields Pro plugin to be installed and activated.', 'iplpro'); ?></p>
        </div>
        <?php
    }
    add_action('admin_notices', 'iplpro_acf_admin_notice');
    return;
}

// Register Match fields
function iplpro_register_match_fields() {
    acf_add_local_field_group(array(
        'key' => 'group_match_details',
        'title' => 'Match Details',
        'fields' => array(
            array(
                'key' => 'field_match_date',
                'label' => 'Match Date & Time',
                'name' => 'match_date',
                'type' => 'date_time_picker',
                'instructions' => 'Select the date and time of the match',
                'required' => 1,
                'display_format' => 'd/m/Y g:i a',
                'return_format' => 'Y-m-d H:i:s',
                'first_day' => 1,
            ),
            array(
                'key' => 'field_team_1',
                'label' => 'Team 1',
                'name' => 'team_1',
                'type' => 'taxonomy',
                'instructions' => 'Select the first team',
                'required' => 1,
                'taxonomy' => 'team',
                'field_type' => 'select',
                'allow_null' => 0,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'id',
            ),
            array(
                'key' => 'field_team_2',
                'label' => 'Team 2',
                'name' => 'team_2',
                'type' => 'taxonomy',
                'instructions' => 'Select the second team',
                'required' => 1,
                'taxonomy' => 'team',
                'field_type' => 'select',
                'allow_null' => 0,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'id',
            ),
            array(
                'key' => 'field_stadium',
                'label' => 'Stadium',
                'name' => 'stadium',
                'type' => 'taxonomy',
                'instructions' => 'Select the stadium where the match will take place',
                'required' => 1,
                'taxonomy' => 'stadium',
                'field_type' => 'select',
                'allow_null' => 0,
                'add_term' => 1,
                'save_terms' => 1,
                'load_terms' => 1,
                'return_format' => 'id',
            ),
            array(
                'key' => 'field_seat_categories',
                'label' => 'Seat Categories',
                'name' => 'seat_categories',
                'type' => 'repeater',
                'instructions' => 'Add seat categories with pricing and availability',
                'required' => 1,
                'collapsed' => 'field_seat_type',
                'min' => 1,
                'max' => 0,
                'layout' => 'block',
                'button_label' => 'Add Seat Category',
                'sub_fields' => array(
                    array(
                        'key' => 'field_seat_type',
                        'label' => 'Type',
                        'name' => 'type',
                        'type' => 'select',
                        'instructions' => 'Select seat category type',
                        'required' => 1,
                        'choices' => array(
                            'General Stand' => 'General Stand',
                            'Premium Stand' => 'Premium Stand',
                            'Pavilion Stand' => 'Pavilion Stand',
                            'VIP Stand' => 'VIP Stand',
                            'Corporate Box' => 'Corporate Box',
                            'Hospitality Box' => 'Hospitality Box',
                            'Skybox Lounge' => 'Skybox Lounge',
                        ),
                        'default_value' => 'General Stand',
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 1,
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_seat_price',
                        'label' => 'Price',
                        'name' => 'price',
                        'type' => 'number',
                        'instructions' => 'Enter the price per seat (in ₹)',
                        'required' => 1,
                        'min' => 0,
                        'step' => 1,
                        'prepend' => '₹',
                    ),
                    array(
                        'key' => 'field_seats_available',
                        'label' => 'Seats Available',
                        'name' => 'seats_available',
                        'type' => 'number',
                        'instructions' => 'Enter the number of seats available',
                        'required' => 1,
                        'min' => 0,
                        'step' => 1,
                    ),
                    array(
                        'key' => 'field_seat_description',
                        'label' => 'Description',
                        'name' => 'description',
                        'type' => 'textarea',
                        'instructions' => 'Enter a description for this seat category',
                        'required' => 0,
                        'rows' => 3,
                    ),
                    array(
                        'key' => 'field_seat_map_image',
                        'label' => 'Seat Map Image',
                        'name' => 'seat_map_image',
                        'type' => 'image',
                        'instructions' => 'Upload an image showing the location of these seats in the stadium',
                        'required' => 0,
                        'return_format' => 'url',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'mime_types' => 'jpg, jpeg, png, svg',
                    ),
                ),
            ),
            array(
                'key' => 'field_match_status',
                'label' => 'Match Status',
                'name' => 'match_status',
                'type' => 'select',
                'instructions' => 'Set the current status of the match',
                'required' => 1,
                'choices' => array(
                    'scheduled' => 'Scheduled',
                    'live' => 'Live',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                    'postponed' => 'Postponed',
                ),
                'default_value' => 'scheduled',
                'allow_null' => 0,
                'multiple' => 0,
                'ui' => 1,
                'return_format' => 'value',
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
        'hide_on_screen' => array(),
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
}
add_action('acf/init', 'iplpro_register_match_fields');

// Register Team fields
function iplpro_register_team_fields() {
    acf_add_local_field_group(array(
        'key' => 'group_team_details',
        'title' => 'Team Details',
        'fields' => array(
            array(
                'key' => 'field_team_logo',
                'label' => 'Team Logo',
                'name' => 'team_logo',
                'type' => 'image',
                'instructions' => 'Upload the team logo',
                'required' => 0,
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'mime_types' => 'jpg, jpeg, png, svg',
            ),
            array(
                'key' => 'field_team_short_name',
                'label' => 'Short Name',
                'name' => 'team_short_name',
                'type' => 'text',
                'instructions' => 'Enter the short name/abbreviation for the team (e.g., CSK, MI)',
                'required' => 1,
                'maxlength' => 3,
            ),
            array(
                'key' => 'field_team_primary_color',
                'label' => 'Primary Color',
                'name' => 'team_primary_color',
                'type' => 'color_picker',
                'instructions' => 'Select the primary team color',
                'required' => 0,
                'default_value' => '#1a2a47',
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
        'hide_on_screen' => array(),
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
}
add_action('acf/init', 'iplpro_register_team_fields');

// Register Stadium fields
function iplpro_register_stadium_fields() {
    acf_add_local_field_group(array(
        'key' => 'group_stadium_details',
        'title' => 'Stadium Details',
        'fields' => array(
            array(
                'key' => 'field_stadium_city',
                'label' => 'City',
                'name' => 'stadium_city',
                'type' => 'text',
                'instructions' => 'Enter the city where the stadium is located',
                'required' => 1,
            ),
            array(
                'key' => 'field_stadium_capacity',
                'label' => 'Capacity',
                'name' => 'stadium_capacity',
                'type' => 'number',
                'instructions' => 'Enter the total capacity of the stadium',
                'required' => 0,
                'min' => 0,
                'step' => 1,
            ),
            array(
                'key' => 'field_stadium_map',
                'label' => 'Stadium Map (SVG)',
                'name' => 'stadium_map',
                'type' => 'textarea',
                'instructions' => 'Enter the SVG code for the stadium map with clickable sections',
                'required' => 0,
                'rows' => 10,
            ),
            array(
                'key' => 'field_stadium_info',
                'label' => 'Stadium Information',
                'name' => 'stadium_info',
                'type' => 'wysiwyg',
                'instructions' => 'Enter detailed information about the stadium',
                'required' => 0,
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
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
        'hide_on_screen' => array(),
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
}
add_action('acf/init', 'iplpro_register_stadium_fields');

// Register Theme Settings
function iplpro_register_theme_settings() {
    acf_add_local_field_group(array(
        'key' => 'group_theme_settings',
        'title' => 'Theme Settings',
        'fields' => array(
            array(
                'key' => 'field_homepage_slider',
                'label' => 'Homepage Slider',
                'name' => 'homepage_slider',
                'type' => 'repeater',
                'instructions' => 'Add slides for the homepage slider',
                'required' => 0,
                'min' => 0,
                'max' => 5,
                'layout' => 'block',
                'button_label' => 'Add Slide',
                'sub_fields' => array(
                    array(
                        'key' => 'field_slider_image',
                        'label' => 'Slide Image',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => 'Upload the slide image',
                        'required' => 1,
                        'return_format' => 'url',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'mime_types' => 'jpg, jpeg, png',
                    ),
                    array(
                        'key' => 'field_slider_title',
                        'label' => 'Slide Title',
                        'name' => 'title',
                        'type' => 'text',
                        'instructions' => 'Enter the slide title',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_slider_subtitle',
                        'label' => 'Slide Subtitle',
                        'name' => 'subtitle',
                        'type' => 'text',
                        'instructions' => 'Enter the slide subtitle',
                        'required' => 0,
                    ),
                    array(
                        'key' => 'field_slider_button_text',
                        'label' => 'Button Text',
                        'name' => 'button_text',
                        'type' => 'text',
                        'instructions' => 'Enter the button text',
                        'required' => 0,
                    ),
                    array(
                        'key' => 'field_slider_button_url',
                        'label' => 'Button URL',
                        'name' => 'button_url',
                        'type' => 'url',
                        'instructions' => 'Enter the button URL',
                        'required' => 0,
                    ),
                ),
            ),
            array(
                'key' => 'field_footer_partners',
                'label' => 'Footer Partners',
                'name' => 'footer_partners',
                'type' => 'repeater',
                'instructions' => 'Add partners for the footer',
                'required' => 0,
                'min' => 0,
                'max' => 0,
                'layout' => 'table',
                'button_label' => 'Add Partner',
                'sub_fields' => array(
                    array(
                        'key' => 'field_partner_title',
                        'label' => 'Title',
                        'name' => 'title',
                        'type' => 'text',
                        'instructions' => 'Enter the partner title (e.g. Official Broadcaster)',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_partner_logo',
                        'label' => 'Logo',
                        'name' => 'logo',
                        'type' => 'image',
                        'instructions' => 'Upload the partner logo',
                        'required' => 1,
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'mime_types' => 'jpg, jpeg, png, svg',
                    ),
                    array(
                        'key' => 'field_partner_name',
                        'label' => 'Name',
                        'name' => 'name',
                        'type' => 'text',
                        'instructions' => 'Enter the partner name',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_partner_url',
                        'label' => 'URL',
                        'name' => 'url',
                        'type' => 'url',
                        'instructions' => 'Enter the partner website URL',
                        'required' => 0,
                    ),
                ),
            ),
            array(
                'key' => 'field_payment_settings',
                'label' => 'Payment Settings',
                'name' => 'payment_settings',
                'type' => 'group',
                'instructions' => 'Configure payment gateway settings',
                'required' => 0,
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'key' => 'field_razorpay_key_id',
                        'label' => 'Razorpay Key ID',
                        'name' => 'razorpay_key_id',
                        'type' => 'text',
                        'instructions' => 'Enter your Razorpay Key ID',
                        'required' => 0,
                    ),
                    array(
                        'key' => 'field_razorpay_key_secret',
                        'label' => 'Razorpay Key Secret',
                        'name' => 'razorpay_key_secret',
                        'type' => 'password',
                        'instructions' => 'Enter your Razorpay Key Secret',
                        'required' => 0,
                    ),
                    array(
                        'key' => 'field_enable_test_mode',
                        'label' => 'Enable Test Mode',
                        'name' => 'enable_test_mode',
                        'type' => 'true_false',
                        'instructions' => 'Toggle test mode for payment gateway',
                        'required' => 0,
                        'ui' => 1,
                        'ui_on_text' => 'Enabled',
                        'ui_off_text' => 'Disabled',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'iplpro-settings',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => array(),
        'active' => true,
        'description' => '',
        'show_in_rest' => 0,
    ));
}
add_action('acf/init', 'iplpro_register_theme_settings');

// Create options page for theme settings
function iplpro_add_options_page() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title'    => 'IPL Pro Settings',
            'menu_title'    => 'IPL Pro Settings',
            'menu_slug'     => 'iplpro-settings',
            'capability'    => 'manage_options',
            'redirect'      => false,
            'icon_url'      => 'dashicons-tickets-alt',
            'position'      => 59,
        ));
    }
}
add_action('acf/init', 'iplpro_add_options_page');
