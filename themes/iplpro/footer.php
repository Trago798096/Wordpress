<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package iplpro
 */

?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer">
		<div class="container">
			<div class="footer-content">
				<div class="footer-column">
					<?php
					// Footer logo
					if (has_custom_logo()) {
						$custom_logo_id = get_theme_mod('custom_logo');
						$logo = wp_get_attachment_image_src($custom_logo_id, 'full');
						if ($logo) {
							echo '<img src="' . esc_url($logo[0]) . '" alt="' . get_bloginfo('name') . '" class="footer-logo">';
						}
					} else {
						echo '<h3 class="site-title">' . get_bloginfo('name') . '</h3>';
					}
					?>
					<p><?php echo esc_html__('Book your IPL 2025 tickets online. Secure your seats for the most exciting cricket tournament in the world.', 'iplpro'); ?></p>
				</div>

				<div class="footer-column">
					<h4 class="footer-title"><?php echo esc_html__('Quick Links', 'iplpro'); ?></h4>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'menu_class'     => 'footer-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				</div>

				<div class="footer-column">
					<h4 class="footer-title"><?php echo esc_html__('Venues', 'iplpro'); ?></h4>
					<ul class="footer-menu">
						<?php
						$stadiums = get_terms(array(
							'taxonomy' => 'stadium',
							'hide_empty' => false,
							'number' => 5,
						));

						if (!is_wp_error($stadiums) && !empty($stadiums)) {
							foreach ($stadiums as $stadium) {
								echo '<li><a href="' . esc_url(get_term_link($stadium)) . '">' . esc_html($stadium->name) . '</a></li>';
							}
						}
						?>
					</ul>
				</div>

				<div class="footer-column">
					<h4 class="footer-title"><?php echo esc_html__('Contact Us', 'iplpro'); ?></h4>
					<ul class="footer-menu">
						<li><?php echo esc_html__('Email: support@ipltickets.com', 'iplpro'); ?></li>
						<li><?php echo esc_html__('Phone: +91 1234567890', 'iplpro'); ?></li>
						<li><?php echo esc_html__('Working Hours: 10AM - 6PM', 'iplpro'); ?></li>
					</ul>
					<div class="footer-social">
						<a href="#" aria-label="Facebook"><span class="dashicons dashicons-facebook-alt"></span></a>
						<a href="#" aria-label="Twitter"><span class="dashicons dashicons-twitter"></span></a>
						<a href="#" aria-label="Instagram"><span class="dashicons dashicons-instagram"></span></a>
						<a href="#" aria-label="YouTube"><span class="dashicons dashicons-youtube"></span></a>
					</div>
				</div>
			</div>

			<div class="footer-bottom">
				<div class="footer-partners">
					<span><?php echo esc_html__('Official Partners:', 'iplpro'); ?></span>
					<img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/sponsors.png'); ?>" alt="<?php echo esc_attr__('Official Partners', 'iplpro'); ?>">
				</div>
				<div class="copyright">
					<?php
					$footer_text = get_theme_mod('iplpro_footer_text', '© ' . date('Y') . ' IPL Pro. All rights reserved.');
					echo wp_kses_post($footer_text);
					?>
				</div>
				<div class="site-info">
					<?php echo esc_html__('Terms & Conditions | Privacy Policy | Refund Policy', 'iplpro'); ?>
				</div>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>