<?php
/**
 * Custom Post Types for IPL Pro Theme
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom post types and taxonomies
 */
function iplpro_register_post_types() {
    // Register Matches Custom Post Type
    register_post_type('match', array(
        'labels' => array(
            'name'               => _x('Matches', 'post type general name', 'iplpro'),
            'singular_name'      => _x('Match', 'post type singular name', 'iplpro'),
            'menu_name'          => _x('Matches', 'admin menu', 'iplpro'),
            'name_admin_bar'     => _x('Match', 'add new on admin bar', 'iplpro'),
            'add_new'            => _x('Add New', 'match', 'iplpro'),
            'add_new_item'       => __('Add New Match', 'iplpro'),
            'new_item'           => __('New Match', 'iplpro'),
            'edit_item'          => __('Edit Match', 'iplpro'),
            'view_item'          => __('View Match', 'iplpro'),
            'all_items'          => __('All Matches', 'iplpro'),
            'search_items'       => __('Search Matches', 'iplpro'),
            'parent_item_colon'  => __('Parent Matches:', 'iplpro'),
            'not_found'          => __('No matches found.', 'iplpro'),
            'not_found_in_trash' => __('No matches found in Trash.', 'iplpro'),
        ),
        'public'              => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'matches'),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-tickets-alt',
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'        => true,
    ));

    // Register Orders Custom Post Type (for storing ticket orders)
    register_post_type('ticket_order', array(
        'labels' => array(
            'name'               => _x('Ticket Orders', 'post type general name', 'iplpro'),
            'singular_name'      => _x('Ticket Order', 'post type singular name', 'iplpro'),
            'menu_name'          => _x('Ticket Orders', 'admin menu', 'iplpro'),
            'name_admin_bar'     => _x('Ticket Order', 'add new on admin bar', 'iplpro'),
            'add_new'            => _x('Add New', 'ticket order', 'iplpro'),
            'add_new_item'       => __('Add New Ticket Order', 'iplpro'),
            'new_item'           => __('New Ticket Order', 'iplpro'),
            'edit_item'          => __('Edit Ticket Order', 'iplpro'),
            'view_item'          => __('View Ticket Order', 'iplpro'),
            'all_items'          => __('All Ticket Orders', 'iplpro'),
            'search_items'       => __('Search Ticket Orders', 'iplpro'),
            'parent_item_colon'  => __('Parent Ticket Orders:', 'iplpro'),
            'not_found'          => __('No ticket orders found.', 'iplpro'),
            'not_found_in_trash' => __('No ticket orders found in Trash.', 'iplpro'),
        ),
        'public'              => false,
        'exclude_from_search' => true,
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array('slug' => 'ticket-orders'),
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-cart',
        'supports'            => array('title', 'custom-fields'),
        'show_in_rest'        => false,
    ));

    // Register Teams Taxonomy
    register_taxonomy('team', array('match'), array(
        'labels' => array(
            'name'                       => _x('Teams', 'taxonomy general name', 'iplpro'),
            'singular_name'              => _x('Team', 'taxonomy singular name', 'iplpro'),
            'search_items'               => __('Search Teams', 'iplpro'),
            'popular_items'              => __('Popular Teams', 'iplpro'),
            'all_items'                  => __('All Teams', 'iplpro'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Team', 'iplpro'),
            'update_item'                => __('Update Team', 'iplpro'),
            'add_new_item'               => __('Add New Team', 'iplpro'),
            'new_item_name'              => __('New Team Name', 'iplpro'),
            'separate_items_with_commas' => __('Separate teams with commas', 'iplpro'),
            'add_or_remove_items'        => __('Add or remove teams', 'iplpro'),
            'choose_from_most_used'      => __('Choose from the most used teams', 'iplpro'),
            'not_found'                  => __('No teams found.', 'iplpro'),
            'menu_name'                  => __('Teams', 'iplpro'),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'team'),
        'show_in_rest'      => true,
    ));

    // Register Stadiums Taxonomy
    register_taxonomy('stadium', array('match'), array(
        'labels' => array(
            'name'                       => _x('Stadiums', 'taxonomy general name', 'iplpro'),
            'singular_name'              => _x('Stadium', 'taxonomy singular name', 'iplpro'),
            'search_items'               => __('Search Stadiums', 'iplpro'),
            'popular_items'              => __('Popular Stadiums', 'iplpro'),
            'all_items'                  => __('All Stadiums', 'iplpro'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Stadium', 'iplpro'),
            'update_item'                => __('Update Stadium', 'iplpro'),
            'add_new_item'               => __('Add New Stadium', 'iplpro'),
            'new_item_name'              => __('New Stadium Name', 'iplpro'),
            'separate_items_with_commas' => __('Separate stadiums with commas', 'iplpro'),
            'add_or_remove_items'        => __('Add or remove stadiums', 'iplpro'),
            'choose_from_most_used'      => __('Choose from the most used stadiums', 'iplpro'),
            'not_found'                  => __('No stadiums found.', 'iplpro'),
            'menu_name'                  => __('Stadiums', 'iplpro'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'stadium'),
        'show_in_rest'      => true,
    ));

    // Add default terms for teams
    $default_teams = array(
        'Chennai Super Kings',
        'Delhi Capitals',
        'Gujarat Titans',
        'Kolkata Knight Riders',
        'Lucknow Super Giants',
        'Mumbai Indians',
        'Punjab Kings',
        'Rajasthan Royals',
        'Royal Challengers Bangalore',
        'Sunrisers Hyderabad',
    );

    // Add default terms for stadiums
    $default_stadiums = array(
        'Wankhede Stadium, Mumbai',
        'M. Chinnaswamy Stadium, Bangalore',
        'Eden Gardens, Kolkata',
        'Arun Jaitley Stadium, Delhi',
        'MA Chidambaram Stadium, Chennai',
        'Punjab Cricket Association Stadium, Mohali',
        'Rajiv Gandhi International Cricket Stadium, Hyderabad',
        'Sawai Mansingh Stadium, Jaipur',
        'BRSABV Ekana Cricket Stadium, Lucknow',
        'Narendra Modi Stadium, Ahmedabad',
    );

    // Add default team terms
    foreach ($default_teams as $team) {
        if (!term_exists($team, 'team')) {
            wp_insert_term($team, 'team');
        }
    }

    // Add default stadium terms
    foreach ($default_stadiums as $stadium) {
        if (!term_exists($stadium, 'stadium')) {
            wp_insert_term($stadium, 'stadium');
        }
    }

    // Flush rewrite rules only on theme activation
    flush_rewrite_rules();
}
add_action('init', 'iplpro_register_post_types');

/**
 * Create custom status for ticket orders
 */
function iplpro_register_ticket_status() {
    register_post_status('pending-payment', array(
        'label'                     => _x('Pending Payment', 'ticket status', 'iplpro'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'iplpro'),
    ));

    register_post_status('payment-verified', array(
        'label'                     => _x('Payment Verified', 'ticket status', 'iplpro'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Payment Verified <span class="count">(%s)</span>', 'Payment Verified <span class="count">(%s)</span>', 'iplpro'),
    ));

    register_post_status('ticket-issued', array(
        'label'                     => _x('Ticket Issued', 'ticket status', 'iplpro'),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Ticket Issued <span class="count">(%s)</span>', 'Ticket Issued <span class="count">(%s)</span>', 'iplpro'),
    ));
}
add_action('init', 'iplpro_register_ticket_status');

/**
 * Add custom status to ticket order dropdown
 */
function iplpro_append_ticket_order_status($post_statuses) {
    global $post;

    if ($post && 'ticket_order' === $post->post_type) {
        $post_statuses['pending-payment'] = _x('Pending Payment', 'ticket status', 'iplpro');
        $post_statuses['payment-verified'] = _x('Payment Verified', 'ticket status', 'iplpro');
        $post_statuses['ticket-issued'] = _x('Ticket Issued', 'ticket status', 'iplpro');
    }

    return $post_statuses;
}
add_filter('display_post_states', 'iplpro_append_ticket_order_status');

/**
 * Add custom columns to ticket order admin list
 */
function iplpro_add_ticket_order_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = $value;
            $new_columns['order_id'] = __('Order ID', 'iplpro');
            $new_columns['match'] = __('Match', 'iplpro');
            $new_columns['customer'] = __('Customer', 'iplpro');
            $new_columns['amount'] = __('Amount', 'iplpro');
            $new_columns['seats'] = __('Seats', 'iplpro');
            $new_columns['utr'] = __('UTR Number', 'iplpro');
        } else if ($key === 'date') {
            $new_columns['payment_date'] = __('Payment Date', 'iplpro');
            $new_columns[$key] = $value;
        } else {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}
add_filter('manage_ticket_order_posts_columns', 'iplpro_add_ticket_order_columns');

/**
 * Populate custom columns for ticket orders
 */
function iplpro_ticket_order_custom_column($column, $post_id) {
    switch ($column) {
        case 'order_id':
            echo esc_html('IPL-' . $post_id);
            break;
            
        case 'match':
            $match_id = get_post_meta($post_id, '_match_id', true);
            if ($match_id) {
                $match = get_post($match_id);
                if ($match) {
                    echo esc_html($match->post_title);
                }
            }
            break;
            
        case 'customer':
            $customer_name = get_post_meta($post_id, '_customer_name', true);
            $customer_email = get_post_meta($post_id, '_customer_email', true);
            if ($customer_name) {
                echo esc_html($customer_name) . '<br>';
                echo esc_html($customer_email);
            }
            break;
            
        case 'amount':
            $amount = get_post_meta($post_id, '_total_amount', true);
            if ($amount) {
                echo '₹' . esc_html($amount);
            }
            break;
            
        case 'seats':
            $ticket_type = get_post_meta($post_id, '_ticket_type', true);
            $quantity = get_post_meta($post_id, '_quantity', true);
            if ($ticket_type && $quantity) {
                echo esc_html($ticket_type) . ' (' . esc_html($quantity) . ')';
            }
            break;
            
        case 'utr':
            $utr = get_post_meta($post_id, '_utr_number', true);
            if ($utr) {
                echo esc_html($utr);
            } else {
                echo '<span class="pending">Awaiting UTR</span>';
            }
            break;
            
        case 'payment_date':
            $payment_date = get_post_meta($post_id, '_payment_date', true);
            if ($payment_date) {
                echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($payment_date)));
            } else {
                echo '<span class="pending">Pending</span>';
            }
            break;
    }
}
add_action('manage_ticket_order_posts_custom_column', 'iplpro_ticket_order_custom_column', 10, 2);

/**
 * Add sorting for custom columns
 */
function iplpro_ticket_order_sortable_columns($columns) {
    $columns['match'] = 'match';
    $columns['amount'] = 'amount';
    $columns['payment_date'] = 'payment_date';
    return $columns;
}
add_filter('manage_edit-ticket_order_sortable_columns', 'iplpro_ticket_order_sortable_columns');

/**
 * Handle custom sorting
 */
function iplpro_ticket_order_orderby($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->get('post_type') === 'ticket_order') {
        if ($query->get('orderby') === 'match') {
            $query->set('meta_key', '_match_id');
            $query->set('orderby', 'meta_value_num');
        } elseif ($query->get('orderby') === 'amount') {
            $query->set('meta_key', '_total_amount');
            $query->set('orderby', 'meta_value_num');
        } elseif ($query->get('orderby') === 'payment_date') {
            $query->set('meta_key', '_payment_date');
            $query->set('orderby', 'meta_value');
        }
    }
}
add_action('pre_get_posts', 'iplpro_ticket_order_orderby');