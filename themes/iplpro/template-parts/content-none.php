<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package iplpro
 */

?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e('Nothing Found', 'iplpro'); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php
		if (is_search()) :
			?>

			<p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'iplpro'); ?></p>
			<?php
			get_search_form();

		elseif (is_home() && current_user_can('publish_posts')) :
			?>

			<p>
				<?php
				printf(
					wp_kses(
						/* translators: 1: link to WP admin new post page. */
						__('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'iplpro'),
						array(
							'a' => array(
								'href' => array(),
							),
						)
					),
					esc_url(admin_url('post-new.php'))
				);
				?>
			</p>

		elseif (is_post_type_archive('match')) :
			?>

			<p><?php esc_html_e('No matches are currently available. Please check back later for upcoming matches.', 'iplpro'); ?></p>

		else :
			?>

			<p><?php esc_html_e('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'iplpro'); ?></p>
			<?php
			get_search_form();

		endif;
		?>
	</div><!-- .page-content -->
</section><!-- .no-results -->