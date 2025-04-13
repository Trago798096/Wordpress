<?php
/**
 * The header for the IPL Pro theme
 *
 * @package iplpro
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <header id="masthead" class="site-header">
        <div class="header-container">
            <div class="site-branding">
                <?php
                if (has_custom_logo()) :
                    the_custom_logo();
                else :
                ?>
                    <h1 class="site-title">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <span class="logo-text">bookMyshow</span>
                        </a>
                    </h1>
                <?php endif; ?>
            </div><!-- .site-branding -->

            <nav id="site-navigation" class="main-navigation">
                <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                    <span class="menu-icon"></span>
                </button>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                ));
                ?>
                <div class="book-now-btn">
                    <a href="<?php echo esc_url(home_url('/matches')); ?>" class="book-btn">Book Now</a>
                </div>
            </nav><!-- #site-navigation -->
        </div><!-- .header-container -->
    </header><!-- #masthead -->

    <?php
    // Include header-match.php only if we're on a match-specific page
    if (is_singular('match') || is_page_template('page-select-seats.php') || 
        is_page_template('page-booking-summary.php') || is_page_template('page-payment.php')) {
        get_template_part('template-parts/header', 'match');
    }
    ?>

    <div id="content" class="site-content">
