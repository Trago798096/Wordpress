<?php
/**
 * Custom Post Types for IPL Pro theme
 *
 * @package iplpro
 */

/**
 * Register custom post types and taxonomies
 */
function iplpro_register_post_types() {
    // Register Match post type
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
            'not_found_in_trash' => __('No matches found in Trash.', 'iplpro')
        ),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'match'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-tickets-alt',
        'supports'           => array('title', 'editor', 'thumbnail')
    ));
    
    // Register Order post type
    register_post_type('order', array(
        'labels' => array(
            'name'               => _x('Orders', 'post type general name', 'iplpro'),
            'singular_name'      => _x('Order', 'post type singular name', 'iplpro'),
            'menu_name'          => _x('Orders', 'admin menu', 'iplpro'),
            'name_admin_bar'     => _x('Order', 'add new on admin bar', 'iplpro'),
            'add_new'            => _x('Add New', 'order', 'iplpro'),
            'add_new_item'       => __('Add New Order', 'iplpro'),
            'new_item'           => __('New Order', 'iplpro'),
            'edit_item'          => __('Edit Order', 'iplpro'),
            'view_item'          => __('View Order', 'iplpro'),
            'all_items'          => __('All Orders', 'iplpro'),
            'search_items'       => __('Search Orders', 'iplpro'),
            'parent_item_colon'  => __('Parent Orders:', 'iplpro'),
            'not_found'          => __('No orders found.', 'iplpro'),
            'not_found_in_trash' => __('No orders found in Trash.', 'iplpro')
        ),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'order'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-cart',
        'supports'           => array('title')
    ));
    
    // Register Team taxonomy
    register_taxonomy('team', 'match', array(
        'labels' => array(
            'name'              => _x('Teams', 'taxonomy general name', 'iplpro'),
            'singular_name'     => _x('Team', 'taxonomy singular name', 'iplpro'),
            'search_items'      => __('Search Teams', 'iplpro'),
            'all_items'         => __('All Teams', 'iplpro'),
            'parent_item'       => __('Parent Team', 'iplpro'),
            'parent_item_colon' => __('Parent Team:', 'iplpro'),
            'edit_item'         => __('Edit Team', 'iplpro'),
            'update_item'       => __('Update Team', 'iplpro'),
            'add_new_item'      => __('Add New Team', 'iplpro'),
            'new_item_name'     => __('New Team Name', 'iplpro'),
            'menu_name'         => __('Teams', 'iplpro'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'team'),
    ));
    
    // Register Stadium taxonomy
    register_taxonomy('stadium', 'match', array(
        'labels' => array(
            'name'              => _x('Stadiums', 'taxonomy general name', 'iplpro'),
            'singular_name'     => _x('Stadium', 'taxonomy singular name', 'iplpro'),
            'search_items'      => __('Search Stadiums', 'iplpro'),
            'all_items'         => __('All Stadiums', 'iplpro'),
            'parent_item'       => __('Parent Stadium', 'iplpro'),
            'parent_item_colon' => __('Parent Stadium:', 'iplpro'),
            'edit_item'         => __('Edit Stadium', 'iplpro'),
            'update_item'       => __('Update Stadium', 'iplpro'),
            'add_new_item'      => __('Add New Stadium', 'iplpro'),
            'new_item_name'     => __('New Stadium Name', 'iplpro'),
            'menu_name'         => __('Stadiums', 'iplpro'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'stadium'),
    ));
    
    // Register Order Status taxonomy
    register_taxonomy('order_status', 'order', array(
        'labels' => array(
            'name'              => _x('Order Statuses', 'taxonomy general name', 'iplpro'),
            'singular_name'     => _x('Order Status', 'taxonomy singular name', 'iplpro'),
            'search_items'      => __('Search Order Statuses', 'iplpro'),
            'all_items'         => __('All Order Statuses', 'iplpro'),
            'parent_item'       => __('Parent Order Status', 'iplpro'),
            'parent_item_colon' => __('Parent Order Status:', 'iplpro'),
            'edit_item'         => __('Edit Order Status', 'iplpro'),
            'update_item'       => __('Update Order Status', 'iplpro'),
            'add_new_item'      => __('Add New Order Status', 'iplpro'),
            'new_item_name'     => __('New Order Status Name', 'iplpro'),
            'menu_name'         => __('Order Statuses', 'iplpro'),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'order-status'),
    ));
}
add_action('init', 'iplpro_register_post_types');

/**
 * Add default terms for taxonomies
 */
function iplpro_add_default_terms() {
    // Add default teams
    $default_teams = array(
        'Mumbai Indians' => array(
            'description' => 'Five-time IPL champions',
            'slug' => 'mumbai-indians'
        ),
        'Chennai Super Kings' => array(
            'description' => 'Four-time IPL champions',
            'slug' => 'chennai-super-kings'
        ),
        'Royal Challengers Bangalore' => array(
            'description' => 'Exciting batting lineup',
            'slug' => 'royal-challengers-bangalore'
        ),
        'Kolkata Knight Riders' => array(
            'description' => 'Two-time IPL champions',
            'slug' => 'kolkata-knight-riders'
        ),
        'Delhi Capitals' => array(
            'description' => 'Young and dynamic team',
            'slug' => 'delhi-capitals'
        ),
        'Rajasthan Royals' => array(
            'description' => 'Inaugural IPL champions',
            'slug' => 'rajasthan-royals'
        ),
        'Punjab Kings' => array(
            'description' => 'Known for aggressive batting',
            'slug' => 'punjab-kings'
        ),
        'Sunrisers Hyderabad' => array(
            'description' => 'Renowned for bowling strength',
            'slug' => 'sunrisers-hyderabad'
        ),
        'Lucknow Super Giants' => array(
            'description' => 'New entrants with strong squad',
            'slug' => 'lucknow-super-giants'
        ),
        'Gujarat Titans' => array(
            'description' => 'Recent champions with balanced team',
            'slug' => 'gujarat-titans'
        )
    );
    
    foreach ($default_teams as $team_name => $team_data) {
        if (!term_exists($team_name, 'team')) {
            wp_insert_term(
                $team_name,
                'team',
                array(
                    'description' => $team_data['description'],
                    'slug' => $team_data['slug']
                )
            );
        }
    }
    
    // Add default stadiums
    $default_stadiums = array(
        'Wankhede Stadium' => array(
            'description' => 'Located in Mumbai, Maharashtra',
            'slug' => 'wankhede-stadium'
        ),
        'M. A. Chidambaram Stadium' => array(
            'description' => 'Located in Chennai, Tamil Nadu',
            'slug' => 'ma-chidambaram-stadium'
        ),
        'Eden Gardens' => array(
            'description' => 'Located in Kolkata, West Bengal',
            'slug' => 'eden-gardens'
        ),
        'M. Chinnaswamy Stadium' => array(
            'description' => 'Located in Bengaluru, Karnataka',
            'slug' => 'm-chinnaswamy-stadium'
        ),
        'Arun Jaitley Stadium' => array(
            'description' => 'Located in Delhi',
            'slug' => 'arun-jaitley-stadium'
        ),
        'Narendra Modi Stadium' => array(
            'description' => 'Located in Ahmedabad, Gujarat',
            'slug' => 'narendra-modi-stadium'
        ),
        'Rajiv Gandhi International Cricket Stadium' => array(
            'description' => 'Located in Hyderabad, Telangana',
            'slug' => 'rajiv-gandhi-international-cricket-stadium'
        ),
        'Punjab Cricket Association Stadium' => array(
            'description' => 'Located in Mohali, Punjab',
            'slug' => 'punjab-cricket-association-stadium'
        ),
        'Sawai Mansingh Stadium' => array(
            'description' => 'Located in Jaipur, Rajasthan',
            'slug' => 'sawai-mansingh-stadium'
        ),
        'BRSABV Ekana Cricket Stadium' => array(
            'description' => 'Located in Lucknow, Uttar Pradesh',
            'slug' => 'brsabv-ekana-cricket-stadium'
        )
    );
    
    foreach ($default_stadiums as $stadium_name => $stadium_data) {
        if (!term_exists($stadium_name, 'stadium')) {
            wp_insert_term(
                $stadium_name,
                'stadium',
                array(
                    'description' => $stadium_data['description'],
                    'slug' => $stadium_data['slug']
                )
            );
        }
    }
    
    // Add default order statuses
    $default_statuses = array(
        'Pending Payment' => array(
            'description' => 'Order received, awaiting payment',
            'slug' => 'pending-payment'
        ),
        'Processing' => array(
            'description' => 'Payment received, processing order',
            'slug' => 'processing'
        ),
        'Completed' => array(
            'description' => 'Order fulfilled and complete',
            'slug' => 'completed'
        ),
        'Cancelled' => array(
            'description' => 'Order cancelled by customer or admin',
            'slug' => 'cancelled'
        ),
        'Failed' => array(
            'description' => 'Payment failed or was declined',
            'slug' => 'failed'
        ),
        'Refunded' => array(
            'description' => 'Order refunded to customer',
            'slug' => 'refunded'
        )
    );
    
    foreach ($default_statuses as $status_name => $status_data) {
        if (!term_exists($status_name, 'order_status')) {
            wp_insert_term(
                $status_name,
                'order_status',
                array(
                    'description' => $status_data['description'],
                    'slug' => $status_data['slug']
                )
            );
        }
    }
}
add_action('init', 'iplpro_add_default_terms');

/**
 * Customize admin columns for Match post type
 */
function iplpro_match_columns($columns) {
    $columns = array(
        'cb' => $columns['cb'],
        'title' => __('Match', 'iplpro'),
        'teams' => __('Teams', 'iplpro'),
        'match_date' => __('Date & Time', 'iplpro'),
        'stadium' => __('Stadium', 'iplpro'),
        'ticket_status' => __('Ticket Status', 'iplpro'),
        'date' => $columns['date']
    );
    
    return $columns;
}
add_filter('manage_match_posts_columns', 'iplpro_match_columns');

/**
 * Display data in custom admin columns for Match post type
 */
function iplpro_match_column_data($column, $post_id) {
    switch ($column) {
        case 'teams':
            $home_team_id = get_field('home_team', $post_id);
            $away_team_id = get_field('away_team', $post_id);
            
            $home_team = iplpro_get_term_name($home_team_id);
            $away_team = iplpro_get_term_name($away_team_id);
            
            if ($home_team && $away_team) {
                echo esc_html($home_team) . ' vs ' . esc_html($away_team);
            } else {
                echo '—';
            }
            break;
            
        case 'match_date':
            $match_date = get_field('match_date', $post_id);
            
            if ($match_date) {
                $match_datetime = DateTime::createFromFormat('d/m/Y g:i a', $match_date);
                if ($match_datetime) {
                    echo esc_html($match_datetime->format('M j, Y - g:i A'));
                } else {
                    echo esc_html($match_date);
                }
            } else {
                echo '—';
            }
            break;
            
        case 'stadium':
            $stadium_id = get_field('stadium', $post_id);
            $stadium = iplpro_get_term_name($stadium_id, 'stadium');
            
            if ($stadium) {
                echo esc_html($stadium);
            } else {
                echo '—';
            }
            break;
            
        case 'ticket_status':
            $ticket_availability = get_field('ticket_availability', $post_id);
            
            if ($ticket_availability) {
                $status_class = 'iplpro-status-' . $ticket_availability;
                $status_text = '';
                
                switch ($ticket_availability) {
                    case 'available':
                        $status_text = 'Available';
                        break;
                    case 'limited':
                        $status_text = 'Limited';
                        break;
                    case 'sold_out':
                        $status_text = 'Sold Out';
                        break;
                }
                
                echo '<span class="' . esc_attr($status_class) . '">' . esc_html($status_text) . '</span>';
            } else {
                echo '<span class="iplpro-status-available">Available</span>';
            }
            break;
    }
}
add_action('manage_match_posts_custom_column', 'iplpro_match_column_data', 10, 2);

/**
 * Customize admin columns for Order post type
 */
function iplpro_order_columns($columns) {
    $columns = array(
        'cb' => $columns['cb'],
        'title' => __('Order ID', 'iplpro'),
        'match' => __('Match', 'iplpro'),
        'customer' => __('Customer', 'iplpro'),
        'tickets' => __('Tickets', 'iplpro'),
        'amount' => __('Amount', 'iplpro'),
        'status' => __('Status', 'iplpro'),
        'date' => $columns['date']
    );
    
    return $columns;
}
add_filter('manage_order_posts_columns', 'iplpro_order_columns');

/**
 * Display data in custom admin columns for Order post type
 */
function iplpro_order_column_data($column, $post_id) {
    switch ($column) {
        case 'match':
            $match_id = get_field('match_id', $post_id);
            
            if ($match_id) {
                echo '<a href="' . esc_url(get_edit_post_link($match_id)) . '">' . esc_html(get_the_title($match_id)) . '</a>';
            } else {
                echo '—';
            }
            break;
            
        case 'customer':
            $customer_name = get_field('customer_name', $post_id);
            $customer_email = get_field('customer_email', $post_id);
            
            if ($customer_name) {
                echo esc_html($customer_name);
                if ($customer_email) {
                    echo '<br><small>' . esc_html($customer_email) . '</small>';
                }
            } else {
                echo '—';
            }
            break;
            
        case 'tickets':
            $ticket_type = get_field('ticket_type', $post_id);
            $quantity = get_field('quantity', $post_id);
            
            if ($ticket_type && $quantity) {
                echo esc_html($quantity) . ' x ' . esc_html($ticket_type);
            } else {
                echo '—';
            }
            break;
            
        case 'amount':
            $total_amount = get_field('total_amount', $post_id);
            
            if ($total_amount) {
                echo '₹' . esc_html(number_format($total_amount));
            } else {
                echo '—';
            }
            break;
            
        case 'status':
            $terms = get_the_terms($post_id, 'order_status');
            
            if (!empty($terms)) {
                $status = $terms[0]->name;
                $slug = $terms[0]->slug;
                echo '<span class="iplpro-order-status iplpro-status-' . esc_attr($slug) . '">' . esc_html($status) . '</span>';
            } else {
                echo '<span class="iplpro-order-status iplpro-status-pending-payment">Pending Payment</span>';
            }
            break;
    }
}
add_action('manage_order_posts_custom_column', 'iplpro_order_column_data', 10, 2);

/**
 * Add admin CSS for custom columns
 */
function iplpro_admin_styles() {
    echo '<style>
        .iplpro-status-available {
            display: inline-block;
            padding: 2px 8px;
            background-color: #36b37e;
            color: #fff;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .iplpro-status-limited {
            display: inline-block;
            padding: 2px 8px;
            background-color: #ffab00;
            color: #fff;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .iplpro-status-sold_out {
            display: inline-block;
            padding: 2px 8px;
            background-color: #ff5630;
            color: #fff;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .iplpro-order-status {
            display: inline-block;
            padding: 2px 8px;
            background-color: #ddd;
            color: #333;
            border-radius: 4px;
            font-size: 12px;
        }
        
        .iplpro-status-completed {
            background-color: #36b37e;
            color: #fff;
        }
        
        .iplpro-status-processing {
            background-color: #00b8d9;
            color: #fff;
        }
        
        .iplpro-status-pending-payment {
            background-color: #ffab00;
            color: #fff;
        }
        
        .iplpro-status-cancelled,
        .iplpro-status-failed {
            background-color: #ff5630;
            color: #fff;
        }
        
        .iplpro-status-refunded {
            background-color: #6554c0;
            color: #fff;
        }
    </style>';
}
add_action('admin_head', 'iplpro_admin_styles');