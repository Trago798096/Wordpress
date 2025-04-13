<?php
/**
 * Template part for displaying match cards
 *
 * @package iplpro
 */

// Get match ID
$match_id = get_the_ID();

// Get match details with error handling
$match = iplpro_get_match_details($match_id);

// If match is not found or data is invalid, skip
if (!$match) {
    return;
}

// Format match date and time
$date_obj = new DateTime($match['date']);
$match_date = $date_obj->format('d F Y'); // Format: 15 April 2025
$match_day = $date_obj->format('l'); // Format: Tuesday
$match_time = $date_obj->format('g:i A T'); // Format: 7:30 PM IST

// Get total available seats
$total_seats = 0;
foreach ($match['seat_categories'] as $category) {
    $total_seats += intval($category['seats_available']);
}

// Check if seats are available
$seats_available = ($total_seats > 0);
$availability_class = $seats_available ? 'has-seats' : 'no-seats';

// Check if match is today
$is_today = (date('Y-m-d') === $date_obj->format('Y-m-d'));
$match_date_display = $is_today ? __('Today', 'iplpro') : $match_date;
if ($is_today) {
    $match_date_display .= ', ' . $match_day;
} else {
    // Check if match is tomorrow
    $tomorrow = new DateTime('tomorrow');
    if ($tomorrow->format('Y-m-d') === $date_obj->format('Y-m-d')) {
        $match_date_display = __('Tomorrow', 'iplpro') . ', ' . $match_day;
    } else {
        $match_date_display = $date_obj->format('d M Y') . ', ' . $match_day;
    }
}
?>

<div class="match-card <?php echo esc_attr($availability_class); ?>">
    <div class="match-date">
        <span class="date-icon">
            <svg viewBox="0 0 24 24" width="18" height="18">
                <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1" fill="currentColor" />
            </svg>
        </span>
        <span class="date-text"><?php echo esc_html($match_date_display); ?></span>
        <span class="time-text">
            <svg viewBox="0 0 24 24" width="18" height="18">
                <path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z" fill="currentColor" />
            </svg>
            <?php echo esc_html($match_time); ?>
        </span>
    </div>
    
    <div class="match-teams">
        <div class="team team-1">
            <div class="team-logo">
                <?php 
                $team1_logo = get_field('team_logo', 'team_' . $match['team1']->term_id);
                if ($team1_logo) {
                    echo '<img src="' . esc_url($team1_logo) . '" alt="' . esc_attr($match['team1']->name) . '">';
                } else {
                    // Fallback SVG if no logo
                    ?>
                    <svg viewBox="0 0 100 100" width="48" height="48">
                        <circle cx="50" cy="50" r="45" fill="#1a2a47" />
                        <text x="50" y="55" text-anchor="middle" fill="white" font-size="24"><?php echo esc_html(substr($match['team1']->name, 0, 2)); ?></text>
                    </svg>
                    <?php
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
                    ?>
                    <svg viewBox="0 0 100 100" width="48" height="48">
                        <circle cx="50" cy="50" r="45" fill="#1a2a47" />
                        <text x="50" y="55" text-anchor="middle" fill="white" font-size="24"><?php echo esc_html(substr($match['team2']->name, 0, 2)); ?></text>
                    </svg>
                    <?php
                }
                ?>
            </div>
            <h3 class="team-name"><?php echo esc_html($match['team2']->name); ?></h3>
        </div>
    </div>
    
    <div class="match-venue">
        <span class="venue-icon">
            <svg viewBox="0 0 24 24" width="18" height="18">
                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" fill="currentColor" />
            </svg>
        </span>
        <span class="venue-name"><?php echo esc_html($match['stadium']->name); ?></span>
    </div>
    
    <div class="match-status">
        <?php if ($seats_available) : ?>
            <span class="seats-available">
                <svg viewBox="0 0 24 24" width="16" height="16">
                    <path d="M9,10V12H7V10H9M13,10V12H11V10H13M17,10V12H15V10H17M19,3A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5C3.89,21 3,20.1 3,19V5A2,2 0 0,1 5,3H6V1H8V3H16V1H18V3H19M19,19V8H5V19H19M9,14V16H7V14H9M13,14V16H11V14H13M17,14V16H15V14H17Z" fill="currentColor" />
                </svg>
                <?php _e('Hurry! Only a Few Left', 'iplpro'); ?>
            </span>
        <?php else : ?>
            <span class="sold-out">
                <svg viewBox="0 0 24 24" width="16" height="16">
                    <path d="M19,3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3M19,19H5V5H19V19M17,11V13H7V11H17Z" fill="currentColor" />
                </svg>
                <?php _e('Sold Out', 'iplpro'); ?>
            </span>
        <?php endif; ?>
    </div>
    
    <div class="match-action">
        <a href="<?php echo esc_url(get_permalink()); ?>" class="book-tickets-btn">
            <?php _e('Book Tickets', 'iplpro'); ?>
        </a>
    </div>
</div>
