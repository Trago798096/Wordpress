<?php
/**
 * The footer for the IPL Pro theme
 *
 * @package iplpro
 */
?>

    </div><!-- #content -->

    <footer id="colophon" class="site-footer">
        <div class="footer-container">
            <div class="footer-partners">
                <div class="partners-section">
                    <h3>Official Broadcaster</h3>
                    <div class="partner-logo">
                        <svg class="star-sports" width="40" height="40" viewBox="0 0 24 24">
                            <path d="M12,2L14.2,7.2L20,8.3L16,12.2L17.2,18L12,15.3L6.8,18L8,12.2L4,8.3L9.8,7.2L12,2Z" fill="#ffffff" />
                        </svg>
                        <span>Star Sports</span>
                    </div>
                </div>
                
                <div class="partners-section">
                    <h3>Title Sponsor</h3>
                    <div class="partner-logo">
                        <svg class="tata" width="40" height="40" viewBox="0 0 24 24">
                            <path d="M3,3H21V21H3V3M5,5V19H19V5H5Z" fill="#ffffff" />
                        </svg>
                        <span>TATA</span>
                    </div>
                </div>
                
                <div class="partners-section">
                    <h3>Official Digital Streaming Partner</h3>
                    <div class="partner-logo">
                        <svg class="jio-cinema" width="40" height="40" viewBox="0 0 24 24">
                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2Z" fill="#ffffff" />
                        </svg>
                        <span>JioCinema</span>
                    </div>
                </div>
                
                <div class="partners-section">
                    <h3>Associate Partner</h3>
                    <div class="partner-logo">
                        <svg class="mygov" width="40" height="40" viewBox="0 0 24 24">
                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2Z" fill="#ffffff" />
                        </svg>
                        <span>MyGov</span>
                    </div>
                </div>
                
                <div class="partners-section">
                    <h3>Official Payment Partner</h3>
                    <div class="partner-logo">
                        <svg class="rupay" width="40" height="40" viewBox="0 0 24 24">
                            <path d="M20,8H4V6H20V8M18,2H6V4H18V2M22,12V20A2,2 0 0,1 20,22H4A2,2 0 0,1 2,20V12A2,2 0 0,1 4,10H20A2,2 0 0,1 22,12M16,16H8V14H16V16Z" fill="#ffffff" />
                        </svg>
                        <span>RuPay</span>
                    </div>
                </div>
                
                <div class="partners-section">
                    <h3>Official Umpire Partner</h3>
                    <div class="partner-logo">
                        <svg class="paytm" width="40" height="40" viewBox="0 0 24 24">
                            <path d="M20,8H4V6H20V8M18,2H6V4H18V2M22,12V20A2,2 0 0,1 20,22H4A2,2 0 0,1 2,20V12A2,2 0 0,1 4,10H20A2,2 0 0,1 22,12M16,16H8V14H16V16Z" fill="#ffffff" />
                        </svg>
                        <span>CEAT</span>
                    </div>
                </div>
            </div><!-- .footer-partners -->
            
            <div class="footer-bottom">
                <div class="footer-menu">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'footer',
                        'menu_id'        => 'footer-menu',
                        'container'      => false,
                        'depth'          => 1,
                    ));
                    ?>
                </div><!-- .footer-menu -->
                
                <div class="site-info">
                    <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
                </div><!-- .site-info -->
            </div><!-- .footer-bottom -->
        </div><!-- .footer-container -->
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
