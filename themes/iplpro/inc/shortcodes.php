<?php
/**
 * Custom shortcodes for IPL Pro theme
 *
 * @package iplpro
 */

/**
 * Shortcode for displaying matches list
 * Usage: [matches_list count="4" status="upcoming"]
 */
function iplpro_matches_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 6,
        'status' => 'upcoming',
        'team' => '',
        'stadium' => ''
    ), $atts, 'matches_list');
    
    $now = new DateTime();
    
    $args = array(
        'post_type' => 'match',
        'posts_per_page' => intval($atts['count']),
        'order' => 'ASC',
        'orderby' => 'meta_value',
        'meta_key' => 'match_date',
    );
    
    // Filter by team if specified
    if (!empty($atts['team'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'team',
            'field' => 'slug',
            'terms' => $atts['team']
        );
    }
    
    // Filter by stadium if specified
    if (!empty($atts['stadium'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'stadium',
            'field' => 'slug',
            'terms' => $atts['stadium']
        );
    }
    
    // Filter by match date (upcoming or past)
    if ($atts['status'] === 'upcoming') {
        $args['meta_query'] = array(
            array(
                'key' => 'match_date',
                'value' => $now->format('d/m/Y g:i a'),
                'compare' => '>=',
                'type' => 'DATETIME'
            )
        );
    } elseif ($atts['status'] === 'past') {
        $args['meta_query'] = array(
            array(
                'key' => 'match_date',
                'value' => $now->format('d/m/Y g:i a'),
                'compare' => '<',
                'type' => 'DATETIME'
            )
        );
        $args['order'] = 'DESC';
    }
    
    $matches_query = new WP_Query($args);
    
    $output = '<div class="upcoming-matches">';
    
    if ($matches_query->have_posts()) {
        while ($matches_query->have_posts()) {
            $matches_query->the_post();
            
            $match_id = get_the_ID();
            $home_team_id = get_field('home_team', $match_id);
            $away_team_id = get_field('away_team', $match_id);
            $stadium_id = get_field('stadium', $match_id);
            $match_date = get_field('match_date', $match_id);
            
            $home_team = iplpro_get_term_name($home_team_id);
            $away_team = iplpro_get_term_name($away_team_id);
            $stadium = iplpro_get_term_name($stadium_id, 'stadium');
            
            $home_team_logo = get_field('team_logo', 'team_' . $home_team_id);
            $away_team_logo = get_field('team_logo', 'team_' . $away_team_id);
            
            // Parse match date
            $match_datetime = DateTime::createFromFormat('d/m/Y g:i a', $match_date);
            $formatted_date = $match_datetime ? $match_datetime->format('D, M j, Y') : '';
            $formatted_time = $match_datetime ? $match_datetime->format('g:i A') : '';
            
            // Check if match is in the past
            $is_past_match = false;
            if ($match_datetime && $match_datetime < $now) {
                $is_past_match = true;
            }
            
            // Get ticket availability
            $ticket_availability = get_field('ticket_availability', $match_id);
            if (empty($ticket_availability)) {
                $ticket_availability = 'available';
            }
            
            $output .= '<div class="match-card">';
            
            // Match date header
            $output .= '<div class="match-date">';
            $output .= '<span class="date-text">' . esc_html($formatted_date) . '</span>';
            $output .= '<span class="time-text">' . esc_html($formatted_time) . '</span>';
            $output .= '</div>';
            
            // Teams
            $output .= '<div class="match-teams">';
            
            // Home team
            $output .= '<div class="team team-home">';
            if ($home_team_logo) {
                $output .= '<img src="' . esc_url($home_team_logo['url']) . '" alt="' . esc_attr($home_team) . '" class="team-logo">';
            } else {
                $output .= '<div class="team-placeholder">' . esc_html(substr($home_team, 0, 2)) . '</div>';
            }
            $output .= '<div class="team-name">' . esc_html($home_team) . '</div>';
            $output .= '</div>';
            
            // VS
            $output .= '<div class="vs-container">';
            $output .= '<span class="vs-text">VS</span>';
            $output .= '</div>';
            
            // Away team
            $output .= '<div class="team team-away">';
            if ($away_team_logo) {
                $output .= '<img src="' . esc_url($away_team_logo['url']) . '" alt="' . esc_attr($away_team) . '" class="team-logo">';
            } else {
                $output .= '<div class="team-placeholder">' . esc_html(substr($away_team, 0, 2)) . '</div>';
            }
            $output .= '<div class="team-name">' . esc_html($away_team) . '</div>';
            $output .= '</div>';
            
            $output .= '</div>'; // End of match-teams
            
            // Venue
            $output .= '<div class="match-venue">';
            $output .= '<span class="dashicons dashicons-location venue-icon"></span>';
            $output .= '<span class="venue-text">' . esc_html($stadium) . '</span>';
            $output .= '</div>';
            
            // Actions
            $output .= '<div class="match-actions">';
            
            // Status badge
            if (!$is_past_match) {
                $status_class = 'status-' . $ticket_availability;
                $status_text = '';
                
                switch ($ticket_availability) {
                    case 'available':
                        $status_text = 'Tickets Available';
                        break;
                    case 'limited':
                        $status_text = 'Limited Tickets';
                        break;
                    case 'sold_out':
                        $status_text = 'Sold Out';
                        break;
                }
                
                $output .= '<span class="match-status ' . esc_attr($status_class) . '">' . esc_html($status_text) . '</span>';
            } else {
                $output .= '<span class="match-status status-sold-out">Match Completed</span>';
            }
            
            // Actions buttons
            if (!$is_past_match && $ticket_availability !== 'sold_out') {
                $output .= '<a href="' . esc_url(get_permalink()) . '" class="book-ticket-btn">Book Tickets</a>';
            } else {
                $output .= '<a href="' . esc_url(get_permalink()) . '" class="view-match-btn">View Details</a>';
            }
            
            $output .= '</div>'; // End of match-actions
            
            $output .= '</div>'; // End of match-card
        }
        wp_reset_postdata();
    } else {
        $output .= '<div class="no-matches">';
        $output .= '<p>' . esc_html__('No matches found.', 'iplpro') . '</p>';
        $output .= '</div>';
    }
    
    $output .= '</div>'; // End of upcoming-matches
    
    return $output;
}
add_shortcode('matches_list', 'iplpro_matches_list_shortcode');

/**
 * Shortcode for displaying teams list
 * Usage: [teams_list]
 */
function iplpro_teams_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'layout' => 'grid',
    ), $atts, 'teams_list');
    
    $teams = get_terms(array(
        'taxonomy' => 'team',
        'hide_empty' => false,
    ));
    
    if (is_wp_error($teams) || empty($teams)) {
        return '<div class="no-teams">' . esc_html__('No teams found.', 'iplpro') . '</div>';
    }
    
    $output = '<div class="teams-list teams-' . esc_attr($atts['layout']) . '">';
    
    foreach ($teams as $team) {
        $team_logo = get_field('team_logo', 'team_' . $team->term_id);
        
        $output .= '<div class="team-item">';
        
        if ($team_logo) {
            $output .= '<img src="' . esc_url($team_logo['url']) . '" alt="' . esc_attr($team->name) . '" class="team-logo">';
        } else {
            $output .= '<div class="team-placeholder">' . esc_html(substr($team->name, 0, 2)) . '</div>';
        }
        
        $output .= '<h3 class="team-name">' . esc_html($team->name) . '</h3>';
        
        // Team description (if available)
        if (!empty($team->description)) {
            $output .= '<div class="team-description">' . wp_kses_post($team->description) . '</div>';
        }
        
        // Team matches link
        $output .= '<a href="' . esc_url(get_term_link($team)) . '" class="team-matches-link">';
        $output .= esc_html__('View Matches', 'iplpro');
        $output .= '</a>';
        
        $output .= '</div>'; // End of team-item
    }
    
    $output .= '</div>'; // End of teams-list
    
    return $output;
}
add_shortcode('teams_list', 'iplpro_teams_list_shortcode');

/**
 * Shortcode for displaying stadiums list
 * Usage: [stadiums_list]
 */
function iplpro_stadiums_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'layout' => 'grid',
    ), $atts, 'stadiums_list');
    
    $stadiums = get_terms(array(
        'taxonomy' => 'stadium',
        'hide_empty' => false,
    ));
    
    if (is_wp_error($stadiums) || empty($stadiums)) {
        return '<div class="no-stadiums">' . esc_html__('No stadiums found.', 'iplpro') . '</div>';
    }
    
    $output = '<div class="stadiums-list stadiums-' . esc_attr($atts['layout']) . '">';
    
    foreach ($stadiums as $stadium) {
        $stadium_image = get_field('stadium_image', 'stadium_' . $stadium->term_id);
        $capacity = get_field('capacity', 'stadium_' . $stadium->term_id);
        $location = get_field('location', 'stadium_' . $stadium->term_id);
        
        $output .= '<div class="stadium-item">';
        
        if ($stadium_image) {
            $output .= '<img src="' . esc_url($stadium_image['url']) . '" alt="' . esc_attr($stadium->name) . '" class="stadium-image">';
        }
        
        $output .= '<h3 class="stadium-name">' . esc_html($stadium->name) . '</h3>';
        
        // Stadium meta info
        $output .= '<div class="stadium-meta">';
        
        if (!empty($location)) {
            $output .= '<div class="stadium-location">';
            $output .= '<span class="dashicons dashicons-location"></span>';
            $output .= '<span>' . esc_html($location) . '</span>';
            $output .= '</div>';
        }
        
        if (!empty($capacity)) {
            $output .= '<div class="stadium-capacity">';
            $output .= '<span class="dashicons dashicons-groups"></span>';
            $output .= '<span>' . esc_html($capacity) . ' ' . esc_html__('Seats', 'iplpro') . '</span>';
            $output .= '</div>';
        }
        
        $output .= '</div>'; // End of stadium-meta
        
        // Stadium description (if available)
        if (!empty($stadium->description)) {
            $output .= '<div class="stadium-description">' . wp_kses_post($stadium->description) . '</div>';
        }
        
        // Stadium matches link
        $output .= '<a href="' . esc_url(get_term_link($stadium)) . '" class="stadium-matches-link">';
        $output .= esc_html__('View Matches', 'iplpro');
        $output .= '</a>';
        
        $output .= '</div>'; // End of stadium-item
    }
    
    $output .= '</div>'; // End of stadiums-list
    
    return $output;
}
add_shortcode('stadiums_list', 'iplpro_stadiums_list_shortcode');