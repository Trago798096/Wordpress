<?php
/**
 * The template for displaying single match
 *
 * @package iplpro
 */

get_header();

// Get match details with error handling
$match_id = get_the_ID();
$match = iplpro_get_match_details($match_id);

// If match is not found or data is invalid, show error
if (!$match) {
    wp_die('Match data not found or is invalid. Please contact the administrator.');
}
?>

<div id="primary" class="content-area match-detail-content">
    <main id="main" class="site-main">

        <article id="post-<?php the_ID(); ?>" <?php post_class('match-article'); ?>>
            <div class="match-detail-container">
                <div class="match-header">
                    <div class="match-teams">
                        <div class="team team-1">
                            <div class="team-logo">
                                <?php 
                                $team1_logo = get_field('team_logo', 'team_' . $match['team1']->term_id);
                                if ($team1_logo) {
                                    echo '<img src="' . esc_url($team1_logo) . '" alt="' . esc_attr($match['team1']->name) . '">';
                                } else {
                                    // Fallback SVG if no logo
                                    echo '<svg viewBox="0 0 100 100" width="64" height="64"><circle cx="50" cy="50" r="45" fill="#1a2a47" /><text x="50" y="55" text-anchor="middle" fill="white" font-size="24">' . esc_html(substr($match['team1']->name, 0, 2)) . '</text></svg>';
                                }
                                ?>
                            </div>
                            <h3 class="team-name"><?php echo esc_html($match['team1']->name); ?></h3>
                        </div>
                        
                        <div class="match-vs">
                            <span>VS</span>
                        </div>
                        
                        <div class="team team-2">
                            <div class="team-logo">
                                <?php 
                                $team2_logo = get_field('team_logo', 'team_' . $match['team2']->term_id);
                                if ($team2_logo) {
                                    echo '<img src="' . esc_url($team2_logo) . '" alt="' . esc_attr($match['team2']->name) . '">';
                                } else {
                                    // Fallback SVG if no logo
                                    echo '<svg viewBox="0 0 100 100" width="64" height="64"><circle cx="50" cy="50" r="45" fill="#1a2a47" /><text x="50" y="55" text-anchor="middle" fill="white" font-size="24">' . esc_html(substr($match['team2']->name, 0, 2)) . '</text></svg>';
                                }
                                ?>
                            </div>
                            <h3 class="team-name"><?php echo esc_html($match['team2']->name); ?></h3>
                        </div>
                    </div>
                    
                    <div class="match-info">
                        <div class="match-date-time">
                            <svg viewBox="0 0 24 24" width="16" height="16">
                                <path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z" fill="currentColor" />
                            </svg>
                            <span>
                                <?php 
                                $date_obj = new DateTime($match['date']);
                                echo esc_html($date_obj->format('d F Y, g:i A T')); 
                                ?>
                            </span>
                        </div>
                        
                        <div class="match-venue">
                            <svg viewBox="0 0 24 24" width="16" height="16">
                                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" fill="currentColor" />
                            </svg>
                            <span><?php echo esc_html($match['stadium']->name); ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="match-ticket-categories">
                    <h3>Ticket Categories</h3>
                    <div class="ticket-categories-container">
                        <?php 
                        if (!empty($match['seat_categories'])) {
                            foreach ($match['seat_categories'] as $category) : 
                                // Check if we have availability
                                $availability_class = ($category['seats_available'] > 0) ? 'available' : 'sold-out';
                                $availability_text = ($category['seats_available'] > 0) ? 'Hurry! Only ' . $category['seats_available'] . ' seats left!' : 'Sold Out';
                        ?>
                            <div class="ticket-category <?php echo esc_attr($availability_class); ?>">
                                <div class="category-header">
                                    <h4><?php echo esc_html($category['type']); ?></h4>
                                    <span class="category-price"><?php echo iplpro_format_price($category['price']); ?></span>
                                </div>
                                <div class="category-details">
                                    <div class="seats-available">
                                        <span><?php echo esc_html($availability_text); ?></span>
                                    </div>
                                    <?php if ($category['seats_available'] > 0) : ?>
                                        <a href="<?php echo esc_url(add_query_arg(array('match_id' => $match_id, 'seat_type' => $category['type']), home_url('/select-seats'))); ?>" class="book-ticket-btn">Book Tickets</a>
                                    <?php else : ?>
                                        <button class="book-ticket-btn disabled" disabled>Sold Out</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        } else {
                            echo '<div class="no-tickets">No ticket categories available for this match.</div>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="match-details">
                    <h3>Match Details</h3>
                    <div class="match-description">
                        <?php the_content(); ?>
                    </div>
                </div>
                
                <div class="stadium-info">
                    <h3>Venue Information</h3>
                    <?php
                    $stadium_info = get_field('stadium_info', 'stadium_' . $match['stadium']->term_id);
                    if ($stadium_info) {
                        echo wp_kses_post($stadium_info);
                    } else {
                        echo '<p>No additional information available for this venue.</p>';
                    }
                    ?>
                </div>
            </div>
        </article>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
