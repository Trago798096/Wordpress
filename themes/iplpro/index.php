<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package iplpro
 */

get_header();
?>

	<main id="primary" class="site-main">
		<div class="container">

		<?php if (is_front_page() && !is_paged()) : ?>
			<!-- Hero Section -->
			<section class="hero-section">
				<div class="hero-slider">
					<div class="hero-slide active">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/hero-1.jpg" alt="IPL 2025" class="hero-image">
						<div class="hero-content">
							<h2 class="hero-title"><?php echo esc_html__('IPL 2025 Tickets', 'iplpro'); ?></h2>
							<p class="hero-description"><?php echo esc_html__('Book your tickets for the most exciting cricket tournament in the world. Experience live action at the stadiums across India.', 'iplpro'); ?></p>
							<a href="<?php echo esc_url(get_post_type_archive_link('match')); ?>" class="hero-button"><?php echo esc_html__('Book Now', 'iplpro'); ?></a>
						</div>
					</div>
					<div class="hero-slide">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/hero-2.jpg" alt="IPL Matches" class="hero-image">
						<div class="hero-content">
							<h2 class="hero-title"><?php echo esc_html__('Watch Your Favorite Teams Live', 'iplpro'); ?></h2>
							<p class="hero-description"><?php echo esc_html__('Secure the best seats to watch your favorite IPL teams compete in electrifying matches across iconic stadiums.', 'iplpro'); ?></p>
							<a href="<?php echo esc_url(get_post_type_archive_link('match')); ?>" class="hero-button"><?php echo esc_html__('View Matches', 'iplpro'); ?></a>
						</div>
					</div>
					<div class="hero-slide">
						<img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/hero-3.jpg" alt="Stadium Experience" class="hero-image">
						<div class="hero-content">
							<h2 class="hero-title"><?php echo esc_html__('Experience Cricket Like Never Before', 'iplpro'); ?></h2>
							<p class="hero-description"><?php echo esc_html__('Premium seating, world-class facilities, and unforgettable moments await you at IPL 2025.', 'iplpro'); ?></p>
							<a href="<?php echo esc_url(get_post_type_archive_link('match')); ?>" class="hero-button"><?php echo esc_html__('Explore Venues', 'iplpro'); ?></a>
						</div>
					</div>
					<div class="slider-nav">
						<span class="slider-dot active" data-slide="0"></span>
						<span class="slider-dot" data-slide="1"></span>
						<span class="slider-dot" data-slide="2"></span>
					</div>
				</div>
			</section>

			<!-- Upcoming Matches Section -->
			<section class="matches-section">
				<h2 class="section-title"><?php echo esc_html__('Upcoming Matches', 'iplpro'); ?></h2>
				<p class="section-description"><?php echo esc_html__('Book tickets for upcoming matches through the match list below', 'iplpro'); ?></p>
				
				<?php echo do_shortcode('[matches_list count="4"]'); ?>
			</section>

			<!-- Features Section -->
			<section class="features-section">
				<div class="container">
					<h2 class="section-title"><?php echo esc_html__('Why Book With Us', 'iplpro'); ?></h2>
					<p class="section-description"><?php echo esc_html__('We provide the best ticket booking experience for IPL matches', 'iplpro'); ?></p>
					
					<div class="features-grid">
						<div class="feature-card">
							<div class="feature-icon"><span class="dashicons dashicons-shield"></span></div>
							<h3 class="feature-title"><?php echo esc_html__('Secure Booking', 'iplpro'); ?></h3>
							<p class="feature-description"><?php echo esc_html__('100% secure payment processing and ticket delivery to your email', 'iplpro'); ?></p>
						</div>
						
						<div class="feature-card">
							<div class="feature-icon"><span class="dashicons dashicons-tickets-alt"></span></div>
							<h3 class="feature-title"><?php echo esc_html__('Best Seats', 'iplpro'); ?></h3>
							<p class="feature-description"><?php echo esc_html__('Select from premium seating options with great views of the action', 'iplpro'); ?></p>
						</div>
						
						<div class="feature-card">
							<div class="feature-icon"><span class="dashicons dashicons-smartphone"></span></div>
							<h3 class="feature-title"><?php echo esc_html__('Easy Payment', 'iplpro'); ?></h3>
							<p class="feature-description"><?php echo esc_html__('Multiple payment options including UPI, cards, and digital wallets', 'iplpro'); ?></p>
						</div>
					</div>
				</div>
			</section>

		<?php else : ?>

			<?php if (have_posts()) : ?>

				<header class="page-header">
					<?php
					the_archive_title('<h1 class="page-title">', '</h1>');
					the_archive_description('<div class="archive-description">', '</div>');
					?>
				</header><!-- .page-header -->

				<div class="post-grid">
					<?php
					/* Start the Loop */
					while (have_posts()) :
						the_post();

						/*
						* Include the Post-Type-specific template for the content.
						* If you want to override this in a child theme, then include a file
						* called content-___.php (where ___ is the Post Type name) and that will be used instead.
						*/
						get_template_part('template-parts/content', get_post_type());

					endwhile;
					?>
				</div>

				<?php
				the_posts_navigation();

			else :

				get_template_part('template-parts/content', 'none');

			endif;
			?>

		<?php endif; ?>

		</div><!-- .container -->
	</main><!-- #main -->

<?php
get_footer();