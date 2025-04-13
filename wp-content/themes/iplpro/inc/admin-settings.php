<?php
/**
 * Admin Settings for IPL Pro theme
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register theme customizer settings
 */
function iplpro_customize_register($wp_customize) {
    // Add IPL Pro section
    $wp_customize->add_section('iplpro_settings', array(
        'title'       => __('IPL Pro Settings', 'iplpro'),
        'description' => __('Customize the appearance and settings for the IPL Pro theme', 'iplpro'),
        'priority'    => 30,
    ));
    
    // Site Logo
    $wp_customize->add_setting('iplpro_logo', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'iplpro_logo', array(
        'label'    => __('Logo', 'iplpro'),
        'section'  => 'iplpro_settings',
        'settings' => 'iplpro_logo',
    )));
    
    // Primary Color
    $wp_customize->add_setting('iplpro_primary_color', array(
        'default'           => '#ff4e00',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'iplpro_primary_color', array(
        'label'    => __('Primary Color', 'iplpro'),
        'section'  => 'iplpro_settings',
        'settings' => 'iplpro_primary_color',
    )));
    
    // Secondary Color
    $wp_customize->add_setting('iplpro_secondary_color', array(
        'default'           => '#1a2a47',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'iplpro_secondary_color', array(
        'label'    => __('Secondary Color', 'iplpro'),
        'section'  => 'iplpro_settings',
        'settings' => 'iplpro_secondary_color',
    )));
    
    // Hero Section Title
    $wp_customize->add_setting('iplpro_hero_title', array(
        'default'           => 'TATA IPL 2025',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('iplpro_hero_title', array(
        'label'    => __('Hero Section Title', 'iplpro'),
        'section'  => 'iplpro_settings',
        'type'     => 'text',
    ));
    
    // Hero Section Subtitle
    $wp_customize->add_setting('iplpro_hero_subtitle', array(
        'default'           => 'Book tickets for upcoming matches',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('iplpro_hero_subtitle', array(
        'label'    => __('Hero Section Subtitle', 'iplpro'),
        'section'  => 'iplpro_settings',
        'type'     => 'text',
    ));
    
    // Footer Text
    $wp_customize->add_setting('iplpro_footer_text', array(
        'default'           => '© ' . date('Y') . ' IPL Pro. All rights reserved.',
        'sanitize_callback' => 'wp_kses_post',
    ));
    
    $wp_customize->add_control('iplpro_footer_text', array(
        'label'    => __('Footer Text', 'iplpro'),
        'section'  => 'iplpro_settings',
        'type'     => 'textarea',
    ));
    
    // GST percentage
    $wp_customize->add_setting('iplpro_gst_percentage', array(
        'default'           => 18,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('iplpro_gst_percentage', array(
        'label'    => __('GST Percentage', 'iplpro'),
        'section'  => 'iplpro_settings',
        'type'     => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 100,
            'step' => 1,
        ),
    ));
    
    // Service Fee
    $wp_customize->add_setting('iplpro_service_fee', array(
        'default'           => 75,
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control('iplpro_service_fee', array(
        'label'    => __('Service Fee (₹)', 'iplpro'),
        'section'  => 'iplpro_settings',
        'type'     => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'step' => 1,
        ),
    ));
}
add_action('customize_register', 'iplpro_customize_register');

/**
 * Add custom CSS to admin
 */
function iplpro_admin_styles() {
    ?>
    <style type="text/css">
        /* Custom styling for Match post type */
        .post-type-match #titlediv .inside {
            display: none;
        }
        
        .post-type-match .acf-field-match-date {
            background-color: #f9f9f9;
            border-left: 4px solid #ff4e00;
            padding-left: 12px !important;
        }
        
        .post-type-match .acf-field-seat-categories {
            background-color: #f6f7f7;
        }
        
        .post-type-match .acf-repeater .acf-row-handle.order {
            background-color: #1a2a47;
            color: #fff;
        }
        
        .post-type-match .acf-repeater .acf-row-handle.remove {
            background-color: #ff4e00;
            color: #fff;
        }
        
        /* IPL Pro admin page */
        .toplevel_page_iplpro-settings .acf-fields > .acf-field {
            border-top: 1px solid #eee;
            padding: 20px 12px;
        }
        
        .toplevel_page_iplpro-settings .acf-fields > .acf-field:first-child {
            border-top: none;
        }
        
        .toplevel_page_iplpro-settings .acf-fields > .acf-field-payment-settings {
            background-color: #f9f9f9;
            border-left: 4px solid #ff4e00;
        }
    </style>
    <?php
}
add_action('admin_head', 'iplpro_admin_styles');

/**
 * Add custom dashboard widget with stats
 */
function iplpro_dashboard_widget() {
    wp_add_dashboard_widget(
        'iplpro_dashboard_widget',
        __('IPL Pro Statistics', 'iplpro'),
        'iplpro_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'iplpro_dashboard_widget');

/**
 * Dashboard widget content
 */
function iplpro_dashboard_widget_content() {
    // Count total matches
    $match_count = wp_count_posts('match');
    $total_matches = $match_count->publish;
    
    // Count upcoming matches
    $today = date('Y-m-d H:i:s');
    $upcoming_args = array(
        'post_type' => 'match',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'match_date',
                'value' => $today,
                'compare' => '>=',
                'type' => 'DATETIME',
            ),
        ),
    );
    $upcoming_query = new WP_Query($upcoming_args);
    $upcoming_matches = $upcoming_query->found_posts;
    
    // Count teams
    $teams_count = wp_count_terms('team', array('hide_empty' => false));
    if (is_wp_error($teams_count)) {
        $teams_count = 0;
    }
    
    // Calculate available seats
    $args = array(
        'post_type' => 'match',
        'posts_per_page' => -1,
    );
    $matches_query = new WP_Query($args);
    $total_seats = 0;
    
    if ($matches_query->have_posts()) {
        while ($matches_query->have_posts()) {
            $matches_query->the_post();
            $seat_categories = get_field('seat_categories');
            
            if (!empty($seat_categories)) {
                foreach ($seat_categories as $category) {
                    $total_seats += intval($category['seats_available']);
                }
            }
        }
        wp_reset_postdata();
    }
    
    // Display stats
    ?>
    <div class="iplpro-dashboard-stats">
        <div class="stat-item">
            <span class="stat-value"><?php echo esc_html($total_matches); ?></span>
            <span class="stat-label"><?php _e('Total Matches', 'iplpro'); ?></span>
        </div>
        
        <div class="stat-item">
            <span class="stat-value"><?php echo esc_html($upcoming_matches); ?></span>
            <span class="stat-label"><?php _e('Upcoming Matches', 'iplpro'); ?></span>
        </div>
        
        <div class="stat-item">
            <span class="stat-value"><?php echo esc_html($teams_count); ?></span>
            <span class="stat-label"><?php _e('Teams', 'iplpro'); ?></span>
        </div>
        
        <div class="stat-item">
            <span class="stat-value"><?php echo esc_html($total_seats); ?></span>
            <span class="stat-label"><?php _e('Available Seats', 'iplpro'); ?></span>
        </div>
    </div>
    
    <div class="iplpro-dashboard-actions">
        <a href="<?php echo esc_url(admin_url('post-new.php?post_type=match')); ?>" class="button button-primary">
            <?php _e('Add New Match', 'iplpro'); ?>
        </a>
        
        <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=team&post_type=match')); ?>" class="button">
            <?php _e('Manage Teams', 'iplpro'); ?>
        </a>
        
        <a href="<?php echo esc_url(admin_url('edit-tags.php?taxonomy=stadium&post_type=match')); ?>" class="button">
            <?php _e('Manage Stadiums', 'iplpro'); ?>
        </a>
    </div>
    
    <style type="text/css">
        .iplpro-dashboard-stats {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .iplpro-dashboard-stats .stat-item {
            flex: 1 0 calc(50% - 20px);
            margin: 10px;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 3px solid #ff4e00;
            text-align: center;
        }
        
        .iplpro-dashboard-stats .stat-value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #1a2a47;
        }
        
        .iplpro-dashboard-stats .stat-label {
            display: block;
            font-size: 14px;
            color: #666;
        }
        
        .iplpro-dashboard-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
    <?php
}

/**
 * Register custom user roles for IPL Pro
 */
function iplpro_register_user_roles() {
    // Add Stadium Manager role
    add_role(
        'stadium_manager',
        __('Stadium Manager', 'iplpro'),
        array(
            'read' => true,
            'edit_posts' => true,
            'edit_others_posts' => true,
            'publish_posts' => false,
            'edit_published_posts' => true,
        )
    );
}
add_action('init', 'iplpro_register_user_roles');

/**
 * Add capabilities to Stadium Manager role
 */
function iplpro_add_role_capabilities() {
    // Get the role object
    $stadium_manager = get_role('stadium_manager');
    
    // Add custom capabilities
    if ($stadium_manager) {
        $stadium_manager->add_cap('edit_match');
        $stadium_manager->add_cap('edit_matches');
        $stadium_manager->add_cap('edit_others_matches');
        $stadium_manager->add_cap('read_match');
        $stadium_manager->add_cap('read_private_matches');
        $stadium_manager->add_cap('edit_published_matches');
    }
}
add_action('admin_init', 'iplpro_add_role_capabilities');

/**
 * Limit Stadium Manager access to assigned stadiums only
 */
function iplpro_limit_stadium_manager_access($query) {
    // Check if we're in admin and the current user is a stadium manager
    if (is_admin() && !current_user_can('administrator') && current_user_can('stadium_manager')) {
        // Get current user
        $user = wp_get_current_user();
        
        // Get assigned stadiums for this user
        $assigned_stadiums = get_user_meta($user->ID, 'assigned_stadiums', true);
        
        // If user has assigned stadiums and we're querying matches
        if (!empty($assigned_stadiums) && $query->get('post_type') === 'match') {
            // Add tax query to limit to assigned stadiums
            $tax_query = array(
                array(
                    'taxonomy' => 'stadium',
                    'field'    => 'term_id',
                    'terms'    => $assigned_stadiums,
                ),
            );
            
            // Set or merge with existing tax query
            $existing_tax_query = $query->get('tax_query');
            if (!empty($existing_tax_query)) {
                $tax_query = array_merge($existing_tax_query, $tax_query);
                $tax_query['relation'] = 'AND';
            }
            
            $query->set('tax_query', $tax_query);
        }
    }
}
add_action('pre_get_posts', 'iplpro_limit_stadium_manager_access');

/**
 * Add stadium assignment meta box for stadium managers
 */
function iplpro_add_user_stadium_meta_box() {
    add_meta_box(
        'iplpro_stadium_assignment',
        __('Stadium Assignment', 'iplpro'),
        'iplpro_stadium_assignment_callback',
        'user-edit',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes_user-edit', 'iplpro_add_user_stadium_meta_box');

/**
 * Callback for stadium assignment meta box
 */
function iplpro_stadium_assignment_callback($user) {
    // Get user's assigned stadiums
    $assigned_stadiums = get_user_meta($user->ID, 'assigned_stadiums', true);
    if (!is_array($assigned_stadiums)) {
        $assigned_stadiums = array();
    }
    
    // Get all stadiums
    $stadiums = get_terms(array(
        'taxonomy' => 'stadium',
        'hide_empty' => false,
    ));
    
    if (is_wp_error($stadiums)) {
        echo '<p>' . esc_html__('No stadiums found.', 'iplpro') . '</p>';
        return;
    }
    
    wp_nonce_field('iplpro_save_stadium_assignment', 'iplpro_stadium_assignment_nonce');
    ?>
    <table class="form-table">
        <tr>
            <th><label for="assigned_stadiums"><?php _e('Assigned Stadiums', 'iplpro'); ?></label></th>
            <td>
                <?php foreach ($stadiums as $stadium) : ?>
                    <label>
                        <input type="checkbox" 
                            name="assigned_stadiums[]" 
                            value="<?php echo esc_attr($stadium->term_id); ?>" 
                            <?php checked(in_array($stadium->term_id, $assigned_stadiums)); ?>>
                        <?php echo esc_html($stadium->name); ?>
                    </label><br>
                <?php endforeach; ?>
                <p class="description"><?php _e('Select the stadiums this user can manage.', 'iplpro'); ?></p>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save stadium assignment meta box
 */
function iplpro_save_stadium_assignment($user_id) {
    // Check if our nonce is set and verify it
    if (!isset($_POST['iplpro_stadium_assignment_nonce']) || !wp_verify_nonce($_POST['iplpro_stadium_assignment_nonce'], 'iplpro_save_stadium_assignment')) {
        return;
    }
    
    // Check if current user can edit user profiles
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }
    
    // Get assigned stadiums from form
    $assigned_stadiums = isset($_POST['assigned_stadiums']) ? array_map('intval', $_POST['assigned_stadiums']) : array();
    
    // Update user meta
    update_user_meta($user_id, 'assigned_stadiums', $assigned_stadiums);
}
add_action('personal_options_update', 'iplpro_save_stadium_assignment');
add_action('edit_user_profile_update', 'iplpro_save_stadium_assignment');

/**
 * Get IPL Pro theme option with default fallback
 */
function iplpro_get_option($option_name, $default = '') {
    $option_value = get_theme_mod($option_name, $default);
    return $option_value;
}

/**
 * Add help tabs for IPL Pro theme
 */
function iplpro_add_help_tabs() {
    // Get the current screen
    $screen = get_current_screen();
    
    // Check if we're on the match edit screen
    if ($screen->post_type === 'match') {
        // Add help tab for match management
        $screen->add_help_tab(array(
            'id'      => 'iplpro_match_help',
            'title'   => __('Match Management', 'iplpro'),
            'content' => '
                <h2>' . __('Managing IPL Matches', 'iplpro') . '</h2>
                <p>' . __('Each match requires the following information:', 'iplpro') . '</p>
                <ul>
                    <li>' . __('Match title: Create a descriptive title for the match', 'iplpro') . '</li>
                    <li>' . __('Match date and time: Select when the match will take place', 'iplpro') . '</li>
                    <li>' . __('Teams: Select the two teams playing in this match', 'iplpro') . '</li>
                    <li>' . __('Stadium: Select where the match will be held', 'iplpro') . '</li>
                    <li>' . __('Seat categories: Add different ticket types with their prices and availability', 'iplpro') . '</li>
                </ul>
                <p>' . __('Make sure to check for proper date formatting and seat inventory before publishing.', 'iplpro') . '</p>
            ',
        ));
        
        // Add help tab for seat categories
        $screen->add_help_tab(array(
            'id'      => 'iplpro_seats_help',
            'title'   => __('Seat Categories', 'iplpro'),
            'content' => '
                <h2>' . __('Managing Seat Categories', 'iplpro') . '</h2>
                <p>' . __('For each seat category, you need to provide:', 'iplpro') . '</p>
                <ul>
                    <li>' . __('Type: The name of the seat category (e.g., "General Stand")', 'iplpro') . '</li>
                    <li>' . __('Price: The cost per ticket in this category', 'iplpro') . '</li>
                    <li>' . __('Seats Available: How many tickets are available in this category', 'iplpro') . '</li>
                    <li>' . __('Description: Optional details about the seating area', 'iplpro') . '</li>
                    <li>' . __('Seat Map Image: An optional image showing the location in the stadium', 'iplpro') . '</li>
                </ul>
                <p>' . __('You can add multiple seat categories for each match with different pricing tiers.', 'iplpro') . '</p>
            ',
        ));
    }
}
add_action('admin_head', 'iplpro_add_help_tabs');
