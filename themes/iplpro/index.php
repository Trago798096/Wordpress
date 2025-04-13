<?php
/**
 * The main template file
 *
 * @package iplpro
 */

get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <?php if (is_front_page()) : ?>
            
            <!-- Hero Slider Section -->
            <section class="hero-slider">
                <div class="slider-container">
                    <div class="slider-item active">
                        <div class="slider-image">
                            <svg viewBox="0 0 1200 400" preserveAspectRatio="xMidYMid slice" class="hero-svg">
                                <rect width="100%" height="100%" fill="#1a2a47"/>
                                <g class="player-silhouettes">
                                    <path d="M200,350 C200,300 220,200 300,180 C350,170 380,200 400,250 C420,300 430,350 450,350 L200,350 Z" fill="#2653a6"/>
                                    <path d="M350,350 C350,300 380,200 450,180 C500,170 530,200 550,250 C570,300 580,350 600,350 L350,350 Z" fill="#f8c01e"/>
                                    <path d="M500,350 C500,300 530,200 600,180 C650,170 680,200 700,250 C720,300 730,350 750,350 L500,350 Z" fill="#ff5e34"/>
                                    <path d="M650,350 C650,300 680,200 750,180 C800,170 830,200 850,250 C870,300 880,350 900,350 L650,350 Z" fill="#0080c8"/>
                                    <path d="M800,350 C800,300 830,200 900,180 C950,170 980,200 1000,250 C1020,300 1030,350 1050,350 L800,350 Z" fill="#7b237a"/>
                                </g>
                                <text x="50%" y="40%" text-anchor="middle" class="hero-title" fill="#ffffff" font-size="45">TATA IPL 2025</text>
                                <text x="50%" y="52%" text-anchor="middle" class="hero-subtitle" fill="#ffffff" font-size="25">BOOK TICKETS NOW</text>
                            </svg>
                        </div>
                        <div class="slider-content">
                            <h2>TATA IPL 2025</h2>
                            <p>Book tickets for upcoming matches through the menu below</p>
                        </div>
                    </div>
                </div>
                <div class="slider-nav">
                    <button class="slider-dot active" data-slide="0"></button>
                    <button class="slider-dot" data-slide="1"></button>
                    <button class="slider-dot" data-slide="2"></button>
                </div>
            </section>
            
            <!-- Tournament Info Section -->
            <section class="tournament-info">
                <div class="tournament-header">
                    <h2>INDIA - Indian Premier League 2025</h2>
                    <div class="tournament-meta">
                        <div class="meta-date">
                            <svg viewBox="0 0 24 24" width="16" height="16">
                                <path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1" fill="currentColor" />
                            </svg>
                            <span>5-27 March 2025 Onwards</span>
                        </div>
                        <div class="meta-venue">
                            <svg viewBox="0 0 24 24" width="16" height="16">
                                <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z" fill="currentColor" />
                            </svg>
                            <span>Multiple Venues</span>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Upcoming Matches Section -->
            <section class="upcoming-matches">
                <div class="section-header">
                    <h2>Upcoming Matches</h2>
                    <p>Book tickets for upcoming matches through the menu below</p>
                </div>
                
                <div class="matches-container">
                    <?php
                    // Query upcoming matches
                    $today = date('Y-m-d H:i:s');
                    $args = array(
                        'post_type' => 'match',
                        'posts_per_page' => 4,
                        'meta_query' => array(
                            array(
                                'key' => 'match_date',
                                'value' => $today,
                                'compare' => '>=',
                                'type' => 'DATETIME',
                            ),
                        ),
                        'meta_key' => 'match_date',
                        'orderby' => 'meta_value',
                        'order' => 'ASC',
                    );
                    
                    $matches_query = new WP_Query($args);
                    
                    if ($matches_query->have_posts()) :
                        while ($matches_query->have_posts()) : $matches_query->the_post();
                            get_template_part('template-parts/content', 'match-card');
                        endwhile;
                        
                        wp_reset_postdata();
                    else :
                        echo '<div class="no-matches">No upcoming matches found.</div>';
                    endif;
                    ?>
                </div>
                
                <div class="view-all-container">
                    <a href="<?php echo esc_url(home_url('/matches')); ?>" class="view-all-btn">View All Matches</a>
                </div>
            </section>
            
        <?php else : ?>
            
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    
                    get_template_part('template-parts/content', get_post_type());
                    
                endwhile;
                
                the_posts_navigation();
                
            else :
                
                get_template_part('template-parts/content', 'none');
                
            endif;
            ?>
            
        <?php endif; ?>
        
    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
