<?php
/**
 * Demo data import for IPL Pro theme
 *
 * @package iplpro
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Function to install demo content on theme activation
 */
function iplpro_install_demo_content() {
    // Check if demo content is already installed
    if (get_option('iplpro_demo_content_installed')) {
        return;
    }
    
    // Create teams
    $teams = array(
        'Mumbai Indians' => array(
            'slug' => 'mumbai-indians',
            'color' => '#004BA0',
            'logo' => get_template_directory_uri() . '/assets/images/teams/mi.png',
        ),
        'Chennai Super Kings' => array(
            'slug' => 'chennai-super-kings',
            'color' => '#FFFF00',
            'logo' => get_template_directory_uri() . '/assets/images/teams/csk.png',
        ),
        'Royal Challengers Bangalore' => array(
            'slug' => 'royal-challengers-bangalore',
            'color' => '#EC1C24',
            'logo' => get_template_directory_uri() . '/assets/images/teams/rcb.png',
        ),
        'Delhi Capitals' => array(
            'slug' => 'delhi-capitals',
            'color' => '#0078BC',
            'logo' => get_template_directory_uri() . '/assets/images/teams/dc.png',
        ),
        'Kolkata Knight Riders' => array(
            'slug' => 'kolkata-knight-riders',
            'color' => '#3A225D',
            'logo' => get_template_directory_uri() . '/assets/images/teams/kkr.png',
        ),
        'Rajasthan Royals' => array(
            'slug' => 'rajasthan-royals',
            'color' => '#254AA5',
            'logo' => get_template_directory_uri() . '/assets/images/teams/rr.png',
        ),
        'Sunrisers Hyderabad' => array(
            'slug' => 'sunrisers-hyderabad',
            'color' => '#F7A721',
            'logo' => get_template_directory_uri() . '/assets/images/teams/srh.png',
        ),
        'Punjab Kings' => array(
            'slug' => 'punjab-kings',
            'color' => '#ED1B24',
            'logo' => get_template_directory_uri() . '/assets/images/teams/pbks.png',
        ),
        'Gujarat Titans' => array(
            'slug' => 'gujarat-titans',
            'color' => '#1B2133',
            'logo' => get_template_directory_uri() . '/assets/images/teams/gt.png',
        ),
        'Lucknow Super Giants' => array(
            'slug' => 'lucknow-super-giants',
            'color' => '#A0E000',
            'logo' => get_template_directory_uri() . '/assets/images/teams/lsg.png',
        ),
    );
    
    $team_ids = array();
    
    foreach ($teams as $team_name => $team_data) {
        // Check if team already exists
        $existing_team = term_exists($team_name, 'team');
        
        if (!$existing_team) {
            $term = wp_insert_term(
                $team_name,
                'team',
                array('slug' => $team_data['slug'])
            );
            
            if (!is_wp_error($term)) {
                $team_id = $term['term_id'];
                $team_ids[$team_name] = $team_id;
                
                // Add team meta
                update_term_meta($team_id, 'team_color', $team_data['color']);
                update_term_meta($team_id, 'team_logo', $team_data['logo']);
            }
        } else {
            $team_ids[$team_name] = $existing_team['term_id'];
        }
    }
    
    // Create stadiums
    $stadiums = array(
        'Wankhede Stadium' => array(
            'slug' => 'wankhede-stadium',
            'city' => 'Mumbai',
            'capacity' => '33,000',
            'image' => get_template_directory_uri() . '/assets/images/stadiums/wankhede.jpg',
        ),
        'M. A. Chidambaram Stadium' => array(
            'slug' => 'ma-chidambaram-stadium',
            'city' => 'Chennai',
            'capacity' => '50,000',
            'image' => get_template_directory_uri() . '/assets/images/stadiums/chepauk.jpg',
        ),
        'Eden Gardens' => array(
            'slug' => 'eden-gardens',
            'city' => 'Kolkata',
            'capacity' => '68,000',
            'image' => get_template_directory_uri() . '/assets/images/stadiums/eden.jpg',
        ),
        'Narendra Modi Stadium' => array(
            'slug' => 'narendra-modi-stadium',
            'city' => 'Ahmedabad',
            'capacity' => '132,000',
            'image' => get_template_directory_uri() . '/assets/images/stadiums/motera.jpg',
        ),
        'Arun Jaitley Stadium' => array(
            'slug' => 'arun-jaitley-stadium',
            'city' => 'Delhi',
            'capacity' => '41,000',
            'image' => get_template_directory_uri() . '/assets/images/stadiums/kotla.jpg',
        ),
    );
    
    $stadium_ids = array();
    
    foreach ($stadiums as $stadium_name => $stadium_data) {
        // Check if stadium already exists
        $existing_stadium = term_exists($stadium_name, 'stadium');
        
        if (!$existing_stadium) {
            $term = wp_insert_term(
                $stadium_name,
                'stadium',
                array('slug' => $stadium_data['slug'])
            );
            
            if (!is_wp_error($term)) {
                $stadium_id = $term['term_id'];
                $stadium_ids[$stadium_name] = $stadium_id;
                
                // Add stadium meta
                update_term_meta($stadium_id, 'stadium_city', $stadium_data['city']);
                update_term_meta($stadium_id, 'stadium_capacity', $stadium_data['capacity']);
                update_term_meta($stadium_id, 'stadium_image', $stadium_data['image']);
            }
        } else {
            $stadium_ids[$stadium_name] = $existing_stadium['term_id'];
        }
    }
    
    // Create matches
    $matches = array(
        array(
            'title' => 'MI vs CSK - Qualifier 1',
            'home_team' => 'Mumbai Indians',
            'away_team' => 'Chennai Super Kings',
            'stadium' => 'Wankhede Stadium',
            'match_date' => date('d/m/Y g:i a', strtotime('+3 days')),
            'match_status' => 'upcoming',
            'ticket_categories' => array(
                array(
                    'ticket_type' => 'Premium',
                    'ticket_price' => 4500,
                    'seats_available' => 100,
                ),
                array(
                    'ticket_type' => 'Corporate Box',
                    'ticket_price' => 8000,
                    'seats_available' => 50,
                ),
                array(
                    'ticket_type' => 'General',
                    'ticket_price' => 2500,
                    'seats_available' => 200,
                ),
            ),
        ),
        array(
            'title' => 'RCB vs DC - Qualifier 2',
            'home_team' => 'Royal Challengers Bangalore',
            'away_team' => 'Delhi Capitals',
            'stadium' => 'M. A. Chidambaram Stadium',
            'match_date' => date('d/m/Y g:i a', strtotime('+5 days')),
            'match_status' => 'upcoming',
            'ticket_categories' => array(
                array(
                    'ticket_type' => 'Premium',
                    'ticket_price' => 4000,
                    'seats_available' => 120,
                ),
                array(
                    'ticket_type' => 'Corporate Box',
                    'ticket_price' => 7500,
                    'seats_available' => 40,
                ),
                array(
                    'ticket_type' => 'General',
                    'ticket_price' => 2000,
                    'seats_available' => 250,
                ),
            ),
        ),
        array(
            'title' => 'KKR vs GT - Eliminator',
            'home_team' => 'Kolkata Knight Riders',
            'away_team' => 'Gujarat Titans',
            'stadium' => 'Eden Gardens',
            'match_date' => date('d/m/Y g:i a', strtotime('+7 days')),
            'match_status' => 'upcoming',
            'ticket_categories' => array(
                array(
                    'ticket_type' => 'Premium',
                    'ticket_price' => 3800,
                    'seats_available' => 150,
                ),
                array(
                    'ticket_type' => 'Corporate Box',
                    'ticket_price' => 7000,
                    'seats_available' => 60,
                ),
                array(
                    'ticket_type' => 'General',
                    'ticket_price' => 1800,
                    'seats_available' => 300,
                ),
            ),
        ),
        array(
            'title' => 'IPL 2025 Final',
            'home_team' => 'Mumbai Indians',
            'away_team' => 'Royal Challengers Bangalore',
            'stadium' => 'Narendra Modi Stadium',
            'match_date' => date('d/m/Y g:i a', strtotime('+10 days')),
            'match_status' => 'upcoming',
            'ticket_categories' => array(
                array(
                    'ticket_type' => 'Premium',
                    'ticket_price' => 6000,
                    'seats_available' => 200,
                ),
                array(
                    'ticket_type' => 'Corporate Box',
                    'ticket_price' => 12000,
                    'seats_available' => 100,
                ),
                array(
                    'ticket_type' => 'General',
                    'ticket_price' => 3500,
                    'seats_available' => 500,
                ),
            ),
        ),
    );
    
    foreach ($matches as $match_data) {
        // Check if the match already exists
        $existing_matches = get_posts(array(
            'post_type' => 'match',
            'post_status' => 'publish',
            'title' => $match_data['title'],
            'posts_per_page' => 1,
        ));
        
        if (empty($existing_matches)) {
            // Create the match
            $match_id = wp_insert_post(array(
                'post_title' => $match_data['title'],
                'post_status' => 'publish',
                'post_type' => 'match',
            ));
            
            if (!is_wp_error($match_id)) {
                // Set match meta
                update_post_meta($match_id, 'home_team', $team_ids[$match_data['home_team']]);
                update_post_meta($match_id, 'away_team', $team_ids[$match_data['away_team']]);
                update_post_meta($match_id, 'stadium', $stadium_ids[$match_data['stadium']]);
                update_post_meta($match_id, 'match_date', $match_data['match_date']);
                update_post_meta($match_id, 'match_status', $match_data['match_status']);
                update_post_meta($match_id, 'ticket_categories', $match_data['ticket_categories']);
            }
        }
    }
    
    // Create required pages
    $pages = array(
        'select-seats' => array(
            'title' => 'Select Seats',
            'content' => '<!-- wp:shortcode -->[stadium_map]<!-- /wp:shortcode -->',
        ),
        'booking-summary' => array(
            'title' => 'Booking Summary',
            'content' => '<!-- wp:shortcode -->[booking_summary]<!-- /wp:shortcode -->',
        ),
        'payment' => array(
            'title' => 'Payment',
            'content' => '<!-- wp:shortcode -->[payment_options]<!-- /wp:shortcode -->',
        ),
        'order-confirmation' => array(
            'title' => 'Order Confirmation',
            'content' => '<!-- wp:shortcode -->[order_confirmation]<!-- /wp:shortcode -->',
        ),
    );
    
    foreach ($pages as $page_slug => $page_data) {
        // Check if the page already exists
        $existing_page = get_page_by_path($page_slug);
        
        if (!$existing_page) {
            // Create the page
            wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_name' => $page_slug,
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => $page_data['content'],
            ));
        }
    }
    
    // Set theme options
    update_option('iplpro_gst_percentage', 18);
    update_option('iplpro_service_fee', 75);
    
    // Mark demo content as installed
    update_option('iplpro_demo_content_installed', true);
}

// Register hooks
add_action('after_switch_theme', 'iplpro_install_demo_content');

// Add a function to manually trigger demo installation
function iplpro_trigger_demo_install() {
    iplpro_install_demo_content();
}