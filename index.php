<?php
/**
 * IPL Pro Ticket Booking Demo Page
 * 
 * This is a demonstration page for the IPL Pro WordPress theme.
 */

// Set page title and meta information
$page_title = "IPL Pro - Book Cricket Match Tickets";
$meta_description = "Book cricket match tickets for TATA IPL 2025. Select from premium seats, VIP pavilions and more.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $meta_description; ?>">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="themes/iplpro/assets/css/main.css">
    <link rel="stylesheet" href="themes/iplpro/assets/css/responsive.css">
    <style>
        /* Demo page specific styles */
        .demo-banner {
            background-color: #1a2a47;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 16px;
        }
        .demo-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .demo-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .demo-heading {
            color: #ff4e00;
            margin-bottom: 15px;
        }
        .theme-features {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .feature-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 15px;
            background-color: #f9f9f9;
        }
        .feature-title {
            color: #1a2a47;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .screens-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .screen-item {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
        }
        .screen-item img {
            width: 100%;
            height: auto;
            display: block;
        }
        .screen-caption {
            padding: 10px;
            background-color: #f5f5f5;
            text-align: center;
            font-size: 14px;
        }
        .navigation-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        .nav-link {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff4e00;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
        }
        .nav-link:hover {
            background-color: #e63e00;
        }
    </style>
</head>
<body>
    <div class="demo-banner">
        This is a demonstration of the IPL Pro WordPress Theme
    </div>

    <header class="site-header">
        <div class="header-container">
            <div class="site-branding">
                <h1 class="site-title">
                    <a href="#" class="logo-text">IPL<span>Pro</span></a>
                </h1>
            </div>
            <nav class="main-navigation">
                <button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false">
                    <span class="menu-icon"></span>
                </button>
                <div id="site-navigation">
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Matches</a></li>
                        <li><a href="#">Venues</a></li>
                        <li><a href="#">Teams</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="book-now-btn">
                    <a href="#" class="book-btn">Book Now</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="demo-content">
        <div class="demo-section">
            <h1 class="demo-heading">IPL Pro WordPress Theme</h1>
            <p>Welcome to the IPL Pro WordPress theme demonstration. This theme is designed to create a ticket booking website for cricket matches, specifically for the Indian Premier League. Below you'll find examples of the various features and components included in the theme.</p>
            
            <div class="navigation-links">
                <a href="themes/iplpro/assets/svg/stadium-map.svg" class="nav-link">View Stadium Map</a>
                <a href="#" class="nav-link">Theme Features</a>
            </div>
        </div>

        <div class="demo-section">
            <h2 class="demo-heading">Key Features</h2>
            <div class="theme-features">
                <div class="feature-card">
                    <h3 class="feature-title">Custom Post Type: Matches</h3>
                    <p>Create and manage cricket match listings with date, venue, teams, and ticket categories.</p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">Interactive Stadium Map</h3>
                    <p>SVG-based clickable stadium map allowing users to select their preferred seating sections.</p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">Responsive Design</h3>
                    <p>Fully responsive layout that looks great on all devices from mobile phones to desktop computers.</p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">Payment Integration</h3>
                    <p>Integration with Razorpay for handling ticket purchases with UPI, credit cards, and more.</p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">Advanced Custom Fields</h3>
                    <p>Extensive use of ACF for creating flexible, user-friendly admin interfaces for match management.</p>
                </div>
                <div class="feature-card">
                    <h3 class="feature-title">Performance Optimized</h3>
                    <p>Optimized assets with lazy loading, minified CSS/JS, and efficient database queries.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-partners">
                <div class="partner-item">
                    <span class="partner-title">Official Broadcaster</span>
                    <img src="https://placehold.co/100x50/1a2a47/ffffff?text=Star+Sports" alt="Star Sports" class="partner-logo">
                </div>
                <div class="partner-item">
                    <span class="partner-title">Title Sponsor</span>
                    <img src="https://placehold.co/100x50/1a2a47/ffffff?text=TATA" alt="TATA" class="partner-logo">
                </div>
                <div class="partner-item">
                    <span class="partner-title">Official Digital Streaming Partner</span>
                    <img src="https://placehold.co/100x50/1a2a47/ffffff?text=JioCinema" alt="JioCinema" class="partner-logo">
                </div>
            </div>
            <div class="footer-menu">
                <ul>
                    <li><a href="#">Terms & Conditions</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-copyright">
                <p>© <?php echo date('Y'); ?> IPL Pro. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="themes/iplpro/assets/js/main.js"></script>
</body>
</html>