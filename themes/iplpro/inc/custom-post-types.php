<?php
/**
 * Custom Post Types for IPL Pro theme
 *
 * @package iplpro
 */

// Register Custom Post Type for Matches
function iplpro_register_match_post_type() {
    $labels = array(
        'name'                  => _x('Matches', 'Post Type General Name', 'iplpro'),
        'singular_name'         => _x('Match', 'Post Type Singular Name', 'iplpro'),
        'menu_name'             => __('Matches', 'iplpro'),
        'name_admin_bar'        => __('Match', 'iplpro'),
        'archives'              => __('Match Archives', 'iplpro'),
        'attributes'            => __('Match Attributes', 'iplpro'),
        'parent_item_colon'     => __('Parent Match:', 'iplpro'),
        'all_items'             => __('All Matches', 'iplpro'),
        'add_new_item'          => __('Add New Match', 'iplpro'),
        'add_new'               => __('Add New', 'iplpro'),
        'new_item'              => __('New Match', 'iplpro'),
        'edit_item'             => __('Edit Match', 'iplpro'),
        'update_item'           => __('Update Match', 'iplpro'),
        'view_item'             => __('View Match', 'iplpro'),
        'view_items'            => __('View Matches', 'iplpro'),
        'search_items'          => __('Search Match', 'iplpro'),
        'not_found'             => __('Not found', 'iplpro'),
        'not_found_in_trash'    => __('Not found in Trash', 'iplpro'),
        'featured_image'        => __('Featured Image', 'iplpro'),
        'set_featured_image'    => __('Set featured image', 'iplpro'),
        'remove_featured_image' => __('Remove featured image', 'iplpro'),
        'use_featured_image'    => __('Use as featured image', 'iplpro'),
        'insert_into_item'      => __('Insert into match', 'iplpro'),
        'uploaded_to_this_item' => __('Uploaded to this match', 'iplpro'),
        'items_list'            => __('Matches list', 'iplpro'),
        'items_list_navigation' => __('Matches list navigation', 'iplpro'),
        'filter_items_list'     => __('Filter matches list', 'iplpro'),
    );
    
    $args = array(
        'label'                 => __('Match', 'iplpro'),
        'description'           => __('Cricket matches for IPL', 'iplpro'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-tickets-alt',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
    );
    
    register_post_type('match', $args);
}
add_action('init', 'iplpro_register_match_post_type');

// Register Taxonomy for Teams
function iplpro_register_team_taxonomy() {
    $labels = array(
        'name'                       => _x('Teams', 'Taxonomy General Name', 'iplpro'),
        'singular_name'              => _x('Team', 'Taxonomy Singular Name', 'iplpro'),
        'menu_name'                  => __('Teams', 'iplpro'),
        'all_items'                  => __('All Teams', 'iplpro'),
        'parent_item'                => __('Parent Team', 'iplpro'),
        'parent_item_colon'          => __('Parent Team:', 'iplpro'),
        'new_item_name'              => __('New Team Name', 'iplpro'),
        'add_new_item'               => __('Add New Team', 'iplpro'),
        'edit_item'                  => __('Edit Team', 'iplpro'),
        'update_item'                => __('Update Team', 'iplpro'),
        'view_item'                  => __('View Team', 'iplpro'),
        'separate_items_with_commas' => __('Separate teams with commas', 'iplpro'),
        'add_or_remove_items'        => __('Add or remove teams', 'iplpro'),
        'choose_from_most_used'      => __('Choose from the most used', 'iplpro'),
        'popular_items'              => __('Popular Teams', 'iplpro'),
        'search_items'               => __('Search Teams', 'iplpro'),
        'not_found'                  => __('Not Found', 'iplpro'),
        'no_terms'                   => __('No teams', 'iplpro'),
        'items_list'                 => __('Teams list', 'iplpro'),
        'items_list_navigation'      => __('Teams list navigation', 'iplpro'),
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'show_in_rest'               => true,
    );
    
    register_taxonomy('team', array('match'), $args);
}
add_action('init', 'iplpro_register_team_taxonomy');

// Register Taxonomy for Stadiums
function iplpro_register_stadium_taxonomy() {
    $labels = array(
        'name'                       => _x('Stadiums', 'Taxonomy General Name', 'iplpro'),
        'singular_name'              => _x('Stadium', 'Taxonomy Singular Name', 'iplpro'),
        'menu_name'                  => __('Stadiums', 'iplpro'),
        'all_items'                  => __('All Stadiums', 'iplpro'),
        'parent_item'                => __('Parent Stadium', 'iplpro'),
        'parent_item_colon'          => __('Parent Stadium:', 'iplpro'),
        'new_item_name'              => __('New Stadium Name', 'iplpro'),
        'add_new_item'               => __('Add New Stadium', 'iplpro'),
        'edit_item'                  => __('Edit Stadium', 'iplpro'),
        'update_item'                => __('Update Stadium', 'iplpro'),
        'view_item'                  => __('View Stadium', 'iplpro'),
        'separate_items_with_commas' => __('Separate stadiums with commas', 'iplpro'),
        'add_or_remove_items'        => __('Add or remove stadiums', 'iplpro'),
        'choose_from_most_used'      => __('Choose from the most used', 'iplpro'),
        'popular_items'              => __('Popular Stadiums', 'iplpro'),
        'search_items'               => __('Search Stadiums', 'iplpro'),
        'not_found'                  => __('Not Found', 'iplpro'),
        'no_terms'                   => __('No stadiums', 'iplpro'),
        'items_list'                 => __('Stadiums list', 'iplpro'),
        'items_list_navigation'      => __('Stadiums list navigation', 'iplpro'),
    );
    
    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => false,
        'show_in_rest'               => true,
    );
    
    register_taxonomy('stadium', array('match'), $args);
}
add_action('init', 'iplpro_register_stadium_taxonomy');

// Create custom fields for Team taxonomy
function iplpro_add_team_meta_fields() {
    // Team Logo field
    register_meta('term', 'team_logo', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ));
    
    // Team Short Name field
    register_meta('term', 'team_short_name', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ));
    
    // Team Primary Color field
    register_meta('term', 'team_primary_color', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ));
}
add_action('init', 'iplpro_add_team_meta_fields');

// Create custom fields for Stadium taxonomy
function iplpro_add_stadium_meta_fields() {
    // Stadium City field
    register_meta('term', 'stadium_city', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ));
    
    // Stadium Capacity field
    register_meta('term', 'stadium_capacity', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'integer',
    ));
    
    // Stadium Map field
    register_meta('term', 'stadium_map', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ));
    
    // Stadium Info field
    register_meta('term', 'stadium_info', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
    ));
}
add_action('init', 'iplpro_add_stadium_meta_fields');

// Add custom columns to match post type
function iplpro_match_columns($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = $value;
            $new_columns['match_date'] = __('Match Date', 'iplpro');
            $new_columns['teams'] = __('Teams', 'iplpro');
            $new_columns['stadium'] = __('Stadium', 'iplpro');
        } else if ($key === 'date') {
            $new_columns['seats_available'] = __('Seats Available', 'iplpro');
            $new_columns[$key] = $value;
        } else {
            $new_columns[$key] = $value;
        }
    }
    return $new_columns;
}
add_filter('manage_match_posts_columns', 'iplpro_match_columns');

// Add content to custom columns
function iplpro_match_custom_column($column, $post_id) {
    switch ($column) {
        case 'match_date':
            $match_date = get_field('match_date', $post_id);
            if ($match_date) {
                $date_obj = new DateTime($match_date);
                echo esc_html($date_obj->format('d M Y, g:i A'));
            } else {
                echo '—';
            }
            break;
            
        case 'teams':
            $team1_id = get_field('team_1', $post_id);
            $team2_id = get_field('team_2', $post_id);
            
            $team1 = iplpro_get_valid_term($team1_id);
            $team2 = iplpro_get_valid_term($team2_id);
            
            if ($team1 && $team2) {
                echo esc_html($team1->name . ' vs ' . $team2->name);
            } else {
                echo '—';
            }
            break;
            
        case 'stadium':
            $stadium_id = get_field('stadium', $post_id);
            $stadium = iplpro_get_valid_term($stadium_id, 'stadium');
            
            if ($stadium) {
                echo esc_html($stadium->name);
            } else {
                echo '—';
            }
            break;
            
        case 'seats_available':
            $seat_categories = get_field('seat_categories', $post_id);
            if (!empty($seat_categories)) {
                $total_seats = 0;
                foreach ($seat_categories as $category) {
                    $total_seats += intval($category['seats_available']);
                }
                echo esc_html($total_seats);
            } else {
                echo '0';
            }
            break;
    }
}
add_action('manage_match_posts_custom_column', 'iplpro_match_custom_column', 10, 2);

// Make columns sortable
function iplpro_match_sortable_columns($columns) {
    $columns['match_date'] = 'match_date';
    $columns['seats_available'] = 'seats_available';
    return $columns;
}
add_filter('manage_edit-match_sortable_columns', 'iplpro_match_sortable_columns');

// Handle pre_get_posts for custom sorting
function iplpro_match_orderby($query) {
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'match') {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    if ('match_date' === $orderby) {
        $query->set('meta_key', 'match_date');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'iplpro_match_orderby');

// Add filter dropdowns for taxonomies in admin
function iplpro_add_taxonomy_filters() {
    global $typenow;
    
    if ($typenow === 'match') {
        // Stadium filter
        $stadium_taxonomy = 'stadium';
        $selected_stadium = isset($_GET[$stadium_taxonomy]) ? sanitize_text_field($_GET[$stadium_taxonomy]) : '';
        $stadium_args = array(
            'show_option_all' => __('All Stadiums', 'iplpro'),
            'taxonomy' => $stadium_taxonomy,
            'name' => $stadium_taxonomy,
            'orderby' => 'name',
            'selected' => $selected_stadium,
            'hierarchical' => true,
            'show_count' => true,
            'hide_empty' => false,
        );
        wp_dropdown_categories($stadium_args);
        
        // Team filter
        $team_taxonomy = 'team';
        $selected_team = isset($_GET[$team_taxonomy]) ? sanitize_text_field($_GET[$team_taxonomy]) : '';
        $team_args = array(
            'show_option_all' => __('All Teams', 'iplpro'),
            'taxonomy' => $team_taxonomy,
            'name' => $team_taxonomy,
            'orderby' => 'name',
            'selected' => $selected_team,
            'hierarchical' => true,
            'show_count' => true,
            'hide_empty' => false,
        );
        wp_dropdown_categories($team_args);
        
        // Date filter
        $date_filter = isset($_GET['match_date_filter']) ? sanitize_text_field($_GET['match_date_filter']) : '';
        ?>
        <select name="match_date_filter">
            <option value="" <?php selected($date_filter, ''); ?>>All Dates</option>
            <option value="upcoming" <?php selected($date_filter, 'upcoming'); ?>>Upcoming Matches</option>
            <option value="past" <?php selected($date_filter, 'past'); ?>>Past Matches</option>
            <option value="today" <?php selected($date_filter, 'today'); ?>>Today's Matches</option>
            <option value="week" <?php selected($date_filter, 'week'); ?>>This Week</option>
            <option value="month" <?php selected($date_filter, 'month'); ?>>This Month</option>
        </select>
        <?php
    }
}
add_action('restrict_manage_posts', 'iplpro_add_taxonomy_filters');

// Handle date filters for matches
function iplpro_match_date_filter($query) {
    global $pagenow, $typenow;
    
    if ($pagenow === 'edit.php' && $typenow === 'match' && isset($_GET['match_date_filter']) && !empty($_GET['match_date_filter'])) {
        $date_filter = sanitize_text_field($_GET['match_date_filter']);
        $today = date('Y-m-d H:i:s');
        
        $meta_query = array();
        
        switch ($date_filter) {
            case 'upcoming':
                $meta_query[] = array(
                    'key' => 'match_date',
                    'value' => $today,
                    'compare' => '>=',
                    'type' => 'DATETIME',
                );
                break;
                
            case 'past':
                $meta_query[] = array(
                    'key' => 'match_date',
                    'value' => $today,
                    'compare' => '<',
                    'type' => 'DATETIME',
                );
                break;
                
            case 'today':
                $meta_query[] = array(
                    'key' => 'match_date',
                    'value' => date('Y-m-d 00:00:00'),
                    'compare' => '>=',
                    'type' => 'DATETIME',
                );
                $meta_query[] = array(
                    'key' => 'match_date',
                    'value' => date('Y-m-d 23:59:59'),
                    'compare' => '<=',
                    'type' => 'DATETIME',
                );
                break;
                
            case 'week':
                $meta_query[] = array(
                    'key' => 'match_date',
                    'value' => date('Y-m-d 00:00:00', strtotime('monday this week')),
                    'compare' => '>=',
                    'type' => 'DATETIME',
                );
                $meta_query[] = array(
                    'key' => 'match_date',
                    'value' => date('Y-m-d 23:59:59', strtotime('sunday this week')),
                    'compare' => '<=',
                    'type' => 'DATETIME',
                );
                break;
                
            case 'month':
                $meta_query[] = array(
                    'key' => 'match_date',
                    'value' => date('Y-m-01 00:00:00'),
                    'compare' => '>=',
                    'type' => 'DATETIME',
                );
                $meta_query[] = array(
                    'key' => 'match_date',
                    'value' => date('Y-m-t 23:59:59'),
                    'compare' => '<=',
                    'type' => 'DATETIME',
                );
                break;
        }
        
        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'iplpro_match_date_filter');
