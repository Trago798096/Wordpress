<?php
/**
 * Template part for displaying match header
 *
 * @package iplpro
 */

// Get current post ID
$current_id = get_queried_object_id();

// Get match ID from different sources
$match_id = 0;

if (is_singular('match')) {
    // If we're on a single match page
    $match_id = get_the_ID();
} elseif (isset($_GET['match_id'])) {
    // If match_id is in query params
    $match_id = intval($_GET['match_id']);
} elseif (isset($_SESSION['iplpro_booking']) && isset($_SESSION['iplpro_booking']['match_id'])) {
    // If we have booking data in session
    $match_id = intval($_SESSION['iplpro_booking']['match_id']);
}

// If no match ID found, don't show match header
if (!$match_id) {
    return;
}

// Get match details with error handling
$match = iplpro_get_match_details($match_id);

// If match is not found or data is invalid, don't show match header
if (!$match) {
    return;
}

// Format match date
$date_obj = new DateTime($match['date']);
$match_date = $date_obj->format('d F Y');
$match_time = $date_obj->format('g:i A T');
?>

<div class="match-header-bar">
    <div class="match-header-container">
        <?php if (is_page_template('page-select-seats.php') || is_page_template('page-booking-summary.php') || is_page_template('page-payment.php')) : ?>
            <a href="<?php echo esc_url(get_permalink($match_id)); ?>" class="back-link">
                <svg viewBox="0 0 24 24" width="24" height="24">
                    <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z" fill="currentColor" />
                </svg>
                <span class="back-text"><?php _e('Back', 'iplpro'); ?></span>
            </a>
        <?php endif; ?>
        
        <div class="match-header-teams">
            <div class="team team-1">
                <div class="team-logo">
                    <?php 
                    $team1_logo = get_field('team_logo', 'team_' . $match['team1']->term_id);
                    if ($team1_logo) {
                        echo '<img src="' . esc_url($team1_logo) . '" alt="' . esc_attr($match['team1']->name) . '">';
                    } else {
                        // Fallback SVG if no logo
                        ?>
                        <svg viewBox="0 0 100 100" width="32" height="32">
                            <circle cx="50" cy="50" r="45" fill="#1a2a47" />
                            <text x="50" y="55" text-anchor="middle" fill="white" font-size="24"><?php echo esc_html(substr($match['team1']->name, 0, 2)); ?></text>
                        </svg>
                        <?php
                    }
                    ?>
                </div>
                <span class="team-name"><?php echo esc_html($match['team1']->name); ?></span>
            </div>
            
            <div class="match-vs">
                <span>vs</span>
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
                        <svg viewBox="0 0 100 100" width="32" height="32">
                            <circle cx="50" cy="50" r="45" fill="#1a2a47" />
                            <text x="50" y="55" text-anchor="middle" fill="white" font-size="24"><?php echo esc_html(substr($match['team2']->name, 0, 2)); ?></text>
                        </svg>
                        <?php
                    }
                    ?>
                </div>
                <span class="team-name"><?php echo esc_html($match['team2']->name); ?></span>
            </div>
        </div>
        
        <div class="match-header-details">
            <div class="match-date-time">
                <span class="match-date-icon">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1" fill="currentColor" />
                    </svg>
                </span>
                <span class="match-date-value"><?php echo esc_html($match_date); ?>, <?php echo esc_html($match_time); ?></span>
            </div>
            
            <div class="match-venue">
                <span class="match-venue-icon">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" fill="currentColor" />
                    </svg>
                </span>
                <span class="match-venue-value"><?php echo esc_html($match['stadium']->name); ?></span>
            </div>
        </div>
        
        <?php 
        // Define step titles and keys
        $steps = array(
            'select-seats' => __('Select Your Seats', 'iplpro'),
            'booking-summary' => __('Complete Your Booking', 'iplpro'),
            'payment' => __('Payment', 'iplpro'),
        );
        
        // Determine current step
        $current_step = '';
        foreach ($steps as $key => $title) {
            if (is_page_template('page-' . $key . '.php')) {
                $current_step = $key;
                break;
            }
        }
        
        // Show steps only if we're in the booking flow
        if (!empty($current_step)) : 
            // Get current step index
            $step_keys = array_keys($steps);
            $current_index = array_search($current_step, $step_keys);
        ?>
            <div class="booking-steps">
                <?php foreach ($steps as $key => $title) : 
                    $step_index = array_search($key, $step_keys);
                    $step_class = ($step_index === $current_index) ? 'active' : 
                                 (($step_index < $current_index) ? 'completed' : '');
                ?>
                    <div class="booking-step <?php echo esc_attr($step_class); ?>">
                        <span class="step-number"><?php echo esc_html($step_index + 1); ?></span>
                        <span class="step-title"><?php echo esc_html($title); ?></span>
                    </div>
                    
                    <?php if ($step_index < count($steps) - 1) : ?>
                        <div class="step-connector <?php echo ($step_index < $current_index) ? 'completed' : ''; ?>"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
