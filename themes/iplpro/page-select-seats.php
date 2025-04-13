<?php
/**
 * Template Name: Select Seats
 *
 * This is the template for the seat selection page
 *
 * @package iplpro
 */

get_header();

// Get match ID from query parameter
$match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
$seat_type = isset($_GET['seat_type']) ? sanitize_text_field($_GET['seat_type']) : '';

// Get match details with error handling
$match = iplpro_get_match_details($match_id);

// If match is not found or data is invalid, redirect to matches page
if (!$match) {
    wp_redirect(home_url('/matches'));
    exit;
}

// Find selected seat category
$selected_category = null;
foreach ($match['seat_categories'] as $category) {
    if ($category['type'] === $seat_type) {
        $selected_category = $category;
        break;
    }
}

// If seat type is not found, use the first one
if (!$selected_category && !empty($match['seat_categories'])) {
    $selected_category = $match['seat_categories'][0];
    $seat_type = $selected_category['type'];
}

// If no seat categories available, redirect
if (!$selected_category) {
    wp_redirect(home_url('/matches'));
    exit;
}
?>

<div id="primary" class="content-area select-seats-content">
    <main id="main" class="site-main">

        <div class="select-seats-container">
            <div class="match-header">
                <div class="teams-heading">
                    <div class="team team-1">
                        <div class="team-logo">
                            <?php 
                            $team1_logo = get_field('team_logo', 'team_' . $match['team1']->term_id);
                            if ($team1_logo) {
                                echo '<img src="' . esc_url($team1_logo) . '" alt="' . esc_attr($match['team1']->name) . '">';
                            } else {
                                // Fallback SVG if no logo
                                echo '<svg viewBox="0 0 100 100" width="40" height="40"><circle cx="50" cy="50" r="45" fill="#1a2a47" /><text x="50" y="55" text-anchor="middle" fill="white" font-size="24">' . esc_html(substr($match['team1']->name, 0, 2)) . '</text></svg>';
                            }
                            ?>
                        </div>
                        <h3 class="team-name"><?php echo esc_html($match['team1']->name); ?></h3>
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
                                echo '<svg viewBox="0 0 100 100" width="40" height="40"><circle cx="50" cy="50" r="45" fill="#1a2a47" /><text x="50" y="55" text-anchor="middle" fill="white" font-size="24">' . esc_html(substr($match['team2']->name, 0, 2)) . '</text></svg>';
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
            
            <div class="selection-tabs">
                <div class="tab-nav">
                    <button class="tab-btn active" data-tab="stadium-map">Stadium Map</button>
                    <button class="tab-btn" data-tab="ticket-types">Ticket Types</button>
                </div>
                
                <div class="tab-content">
                    <div class="tab-pane active" id="stadium-map">
                        <h3>Select a section from the stadium map</h3>
                        <p>Click on a section to select your preferred seating area</p>
                        
                        <div class="stadium-map-container">
                            <h4><?php echo esc_html($match['team1']->name); ?> vs <?php echo esc_html($match['team2']->name); ?></h4>
                            <p>Venue: <?php echo esc_html($match['stadium']->name); ?></p>
                            
                            <?php
                            // Get stadium map SVG for this stadium
                            $stadium_map = get_field('stadium_map', 'stadium_' . $match['stadium']->term_id);
                            if ($stadium_map) {
                                echo '<div class="stadium-map">' . $stadium_map . '</div>';
                            } else {
                                // Fallback generic stadium SVG
                                ?>
                                <div class="stadium-map">
                                    <svg viewBox="0 0 500 500" width="100%" height="300">
                                        <circle cx="250" cy="250" r="100" fill="#8BC34A" stroke="#555" stroke-width="2" />
                                        <circle cx="250" cy="250" r="95" fill="#8BC34A" stroke="#fff" stroke-width="1" />
                                        <circle cx="250" cy="250" r="20" fill="#DDD" stroke="#555" stroke-width="1" />
                                        
                                        <!-- First layer sections -->
                                        <path d="M250,150 A100,100 0 0,1 350,250 L250,250 Z" fill="#FFB74D" stroke="#555" stroke-width="1" class="section-path" data-section="Premium North" />
                                        <path d="M350,250 A100,100 0 0,1 250,350 L250,250 Z" fill="#FF8A65" stroke="#555" stroke-width="1" class="section-path" data-section="Premium East" />
                                        <path d="M250,350 A100,100 0 0,1 150,250 L250,250 Z" fill="#FFB74D" stroke="#555" stroke-width="1" class="section-path" data-section="Premium South" />
                                        <path d="M150,250 A100,100 0 0,1 250,150 L250,250 Z" fill="#FF8A65" stroke="#555" stroke-width="1" class="section-path" data-section="Premium West" />
                                        
                                        <!-- Outer ring sections -->
                                        <path d="M250,150 A100,100 0 0,0 150,250 L120,250 A130,130 0 0,1 250,120 Z" fill="#90CAF9" stroke="#555" stroke-width="1" class="section-path" data-section="General North-West" />
                                        <path d="M150,250 A100,100 0 0,0 250,350 L250,380 A130,130 0 0,1 120,250 Z" fill="#CE93D8" stroke="#555" stroke-width="1" class="section-path" data-section="General South-West" />
                                        <path d="M250,350 A100,100 0 0,0 350,250 L380,250 A130,130 0 0,1 250,380 Z" fill="#90CAF9" stroke="#555" stroke-width="1" class="section-path" data-section="General South-East" />
                                        <path d="M350,250 A100,100 0 0,0 250,150 L250,120 A130,130 0 0,1 380,250 Z" fill="#CE93D8" stroke="#555" stroke-width="1" class="section-path" data-section="General North-East" />
                                        
                                        <!-- Legend -->
                                        <circle cx="120" cy="420" r="10" fill="#FFB74D" />
                                        <text x="140" y="425" font-size="12" fill="#333">VIP Pavilion</text>
                                        
                                        <circle cx="120" cy="450" r="10" fill="#90CAF9" />
                                        <text x="140" y="455" font-size="12" fill="#333">Premium Blocks</text>
                                        
                                        <circle cx="250" cy="420" r="10" fill="#CE93D8" />
                                        <text x="270" y="425" font-size="12" fill="#333">Club House</text>
                                    </svg>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="ticket-types">
                        <h3>Select a ticket type</h3>
                        <p>Choose from our available ticket categories</p>
                        
                        <div class="ticket-types-container">
                            <?php 
                            if (!empty($match['seat_categories'])) {
                                foreach ($match['seat_categories'] as $category) : 
                                    // Check if we have availability
                                    $availability_class = ($category['seats_available'] > 0) ? 'available' : 'sold-out';
                                    $selected_class = ($category['type'] === $seat_type) ? 'selected' : '';
                            ?>
                                <div class="ticket-type <?php echo esc_attr($availability_class . ' ' . $selected_class); ?>" 
                                    data-type="<?php echo esc_attr($category['type']); ?>"
                                    data-price="<?php echo esc_attr($category['price']); ?>">
                                    <div class="ticket-type-header">
                                        <h4>Price: <?php echo iplpro_format_price($category['price']); ?></h4>
                                        <span class="seats-available"><?php echo esc_html($category['seats_available']); ?> seats available</span>
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
                </div>
            </div>
            
            <div class="booking-form" id="booking-summary">
                <h3>Booking Summary</h3>
                
                <div class="ticket-options">
                    <div class="ticket-type-options">
                        <div class="ticket-type-option selected" data-type="<?php echo esc_attr($selected_category['type']); ?>">
                            <?php echo esc_html($selected_category['type']); ?>
                        </div>
                        
                        <?php
                        // Add other ticket type options
                        foreach ($match['seat_categories'] as $category) {
                            if ($category['type'] !== $selected_category['type'] && $category['seats_available'] > 0) {
                                echo '<div class="ticket-type-option" data-type="' . esc_attr($category['type']) . '">' . esc_html($category['type']) . '</div>';
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="ticket-info">
                        <div class="ticket-description">
                            <p><?php echo esc_html($selected_category['type']); ?> - <?php echo esc_html($selected_category['description'] ?? 'Affordable seating, usually in the upper stands.'); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="ticket-details">
                    <div class="price-item">
                        <span class="label">Price per Ticket:</span>
                        <span class="value price-value"><?php echo iplpro_format_price($selected_category['price']); ?></span>
                    </div>
                    
                    <div class="quantity-item">
                        <span class="label">Quantity:</span>
                        <div class="quantity-control">
                            <button class="quantity-btn minus" type="button">-</button>
                            <input type="number" class="quantity-input" value="1" min="1" max="<?php echo esc_attr($selected_category['seats_available']); ?>">
                            <button class="quantity-btn plus" type="button">+</button>
                        </div>
                    </div>
                    
                    <div class="total-item">
                        <span class="label">Total:</span>
                        <span class="value total-value"><?php echo iplpro_format_price($selected_category['price']); ?></span>
                    </div>
                </div>
                
                <div class="booking-actions">
                    <form id="proceed-booking-form" action="<?php echo esc_url(home_url('/booking-summary')); ?>" method="get">
                        <input type="hidden" name="match_id" value="<?php echo esc_attr($match_id); ?>">
                        <input type="hidden" name="seat_type" value="<?php echo esc_attr($seat_type); ?>">
                        <input type="hidden" name="quantity" value="1" id="quantity-field">
                        <button type="submit" class="proceed-btn">Proceed to Booking</button>
                    </form>
                </div>
            </div>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
