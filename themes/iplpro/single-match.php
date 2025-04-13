<?php
/**
 * The template for displaying single match
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package iplpro
 */

get_header();
?>

	<main id="primary" class="site-main">
		<div class="container">

		<?php
		while (have_posts()) :
			the_post();
			
			$match_id = get_the_ID();
			$home_team_id = get_field('home_team');
			$away_team_id = get_field('away_team');
			$stadium_id = get_field('stadium');
			$match_date = get_field('match_date');
			
			$home_team = iplpro_get_term_name($home_team_id);
			$away_team = iplpro_get_term_name($away_team_id);
			$stadium = iplpro_get_term_name($stadium_id, 'Stadium');
			
			// Check if match date is in the past
			$is_past_match = false;
			if ($match_date) {
				$match_datetime = DateTime::createFromFormat('d/m/Y g:i a', $match_date);
				$now = new DateTime();
				
				if ($match_datetime && $match_datetime < $now) {
					$is_past_match = true;
				}
			}
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<div class="match-back-link">
						<a href="<?php echo esc_url(get_post_type_archive_link('match')); ?>" class="back-link">
							<span class="dashicons dashicons-arrow-left-alt"></span> <?php esc_html_e('Back to Matches', 'iplpro'); ?>
						</a>
					</div>

					<h1 class="entry-title screen-reader-text"><?php the_title(); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php iplpro_match_details($match_id); ?>
					
					<?php if ($is_past_match) : ?>
						<div class="past-match-notice">
							<p><?php esc_html_e('This match has already been played. Tickets are no longer available.', 'iplpro'); ?></p>
						</div>
					<?php else : ?>
						<div class="seat-selection-container">
							<div class="tab-navigation">
								<button class="tab-button active" data-tab="stadium-map"><?php esc_html_e('Stadium Map', 'iplpro'); ?></button>
								<button class="tab-button" data-tab="ticket-types"><?php esc_html_e('Ticket Types', 'iplpro'); ?></button>
							</div>
							
							<div class="tab-content active" data-tab="stadium-map">
								<?php iplpro_stadium_map(); ?>
							</div>
							
							<div class="tab-content" data-tab="ticket-types">
								<?php iplpro_ticket_categories($match_id); ?>
							</div>
							
							<?php iplpro_booking_form($match_id); ?>
						</div>
					<?php endif; ?>
				</div><!-- .entry-content -->

				<footer class="entry-footer">
					<?php 
					// Additional information about the match if needed
					the_content();
					?>
				</footer><!-- .entry-footer -->
			</article><!-- #post-<?php the_ID(); ?> -->

		<?php endwhile; // End of the loop. ?>

		</div><!-- .container -->
	</main><!-- #main -->

<?php
get_footer();