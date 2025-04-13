<?php
/**
 * Template Name: Payment
 *
 * This is the template for the payment page
 *
 * @package iplpro
 */

get_header();

// Get booking details from query parameters
$match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;
$seat_type = isset($_GET['seat_type']) ? sanitize_text_field($_GET['seat_type']) : '';
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
$total = isset($_GET['total']) ? floatval($_GET['total']) : 0;
$fullname = isset($_GET['fullname']) ? sanitize_text_field($_GET['fullname']) : '';
$email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';
$phone = isset($_GET['phone']) ? sanitize_text_field($_GET['phone']) : '';

// Get match details with error handling
$match = iplpro_get_match_details($match_id);

// If match is not found or data is invalid, redirect to matches page
if (!$match || empty($seat_type) || $quantity < 1 || $total <= 0) {
    wp_redirect(home_url('/matches'));
    exit;
}

// Check if customer information is provided
if (empty($fullname) || empty($email) || empty($phone)) {
    wp_redirect(add_query_arg(array(
        'match_id' => $match_id,
        'seat_type' => $seat_type,
        'quantity' => $quantity,
    ), home_url('/booking-summary')));
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

// If seat type is not found or not enough seats available, redirect
if (!$selected_category || $selected_category['seats_available'] < $quantity) {
    wp_redirect(home_url('/matches'));
    exit;
}

// Calculate pricing to double-check (security measure)
$price_details = iplpro_calculate_total($selected_category['price'], $quantity);

// Generate a transaction ID for reference
$transaction_id = 'txn_' . substr(md5(uniqid(rand(), true)), 0, 16);

// Apply a discount for demo (10% off)
$discount = round($price_details['total'] * 0.1, 2);
$discounted_total = $price_details['total'] - $discount;
?>

<div id="primary" class="content-area payment-content">
    <main id="main" class="site-main">

        <div class="payment-container">
            <div class="payment-header">
                <h1>IPL Tickets</h1>
                <div class="payment-offer">
                    <svg viewBox="0 0 24 24" width="16" height="16">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z" fill="currentColor" />
                    </svg>
                    <span>Razorpay Trusted Business</span>
                </div>
            </div>
            
            <div class="payment-info">
                <div class="payment-heading">
                    <h2>UPI/QR: Pay with GPay & grab up to ₹400 instantly! 💰💸</h2>
                </div>
                
                <div class="payment-options-tabs">
                    <div class="tabs-header">
                        <button class="tab-btn active" data-tab="upi">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path d="M17,18C15.89,18 15,18.89 15,20A2,2 0 0,0 17,22A2,2 0 0,0 19,20C19,18.89 18.1,18 17,18M1,2V4H3L6.6,11.59L5.24,14.04C5.09,14.32 5,14.65 5,15A2,2 0 0,0 7,17H19V15H7.42A0.25,0.25 0 0,1 7.17,14.75C7.17,14.7 7.18,14.66 7.2,14.63L8.1,13H15.55C16.3,13 16.96,12.58 17.3,11.97L20.88,5.5C20.95,5.34 21,5.17 21,5A1,1 0 0,0 20,4H5.21L4.27,2M7,18C5.89,18 5,18.89 5,20A2,2 0 0,0 7,22A2,2 0 0,0 9,20C9,18.89 8.1,18 7,18Z" fill="currentColor" />
                            </svg>
                            UPI/QR
                            <span class="payment-icons">♦ 🔵 💎 🔹</span>
                        </button>
                        <button class="tab-btn" data-tab="cards">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" fill="currentColor" />
                            </svg>
                            Cards
                            <span class="card-icons">💳 💳 💳</span>
                        </button>
                        <button class="tab-btn" data-tab="wallets">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path d="M21,18V19A2,2 0 0,1 19,21H5C3.89,21 3,20.1 3,19V5A2,2 0 0,1 5,3H19A2,2 0 0,1 21,5V6H12C10.89,6 10,6.9 10,8V16A2,2 0 0,0 12,18M12,16H22V8H12M16,13.5A1.5,1.5 0 0,1 14.5,12A1.5,1.5 0 0,1 16,10.5A1.5,1.5 0 0,1 17.5,12A1.5,1.5 0 0,1 16,13.5Z" fill="currentColor" />
                            </svg>
                            Wallet
                            <span class="wallet-icons">🔵 💎 🔹</span>
                        </button>
                    </div>
                    
                    <div class="tabs-content">
                        <div class="tab-pane active" id="upi-tab">
                            <div class="upi-content">
                                <div class="upi-info">
                                    <p>Pay using any UPI App to make your order success ✓</p>
                                </div>
                                
                                <div class="qr-code">
                                    <svg viewBox="0 0 200 200" width="150" height="150">
                                        <!-- QR Code SVG representation -->
                                        <rect x="0" y="0" width="200" height="200" fill="#ffffff" />
                                        <g fill="#000000">
                                            <!-- Create a grid pattern that resembles a QR code -->
                                            <?php 
                                            // Generate a realistic QR code pattern
                                            for ($i = 0; $i < 8; $i++) {
                                                for ($j = 0; $j < 8; $j++) {
                                                    if (rand(0, 1) || ($i < 2 && $j < 2) || ($i < 2 && $j > 5) || ($i > 5 && $j < 2)) {
                                                        $x = $i * 20 + 10;
                                                        $y = $j * 20 + 10;
                                                        echo '<rect x="' . $x . '" y="' . $y . '" width="20" height="20" />';
                                                    }
                                                }
                                            }
                                            // Add QR finder patterns
                                            echo '<rect x="10" y="10" width="60" height="60" />';
                                            echo '<rect x="20" y="20" width="40" height="40" fill="#ffffff" />';
                                            echo '<rect x="30" y="30" width="20" height="20" />';
                                            
                                            echo '<rect x="130" y="10" width="60" height="60" />';
                                            echo '<rect x="140" y="20" width="40" height="40" fill="#ffffff" />';
                                            echo '<rect x="150" y="30" width="20" height="20" />';
                                            
                                            echo '<rect x="10" y="130" width="60" height="60" />';
                                            echo '<rect x="20" y="140" width="40" height="40" fill="#ffffff" />';
                                            echo '<rect x="30" y="150" width="20" height="20" />';
                                            ?>
                                        </g>
                                    </svg>
                                </div>
                                
                                <div class="upi-details">
                                    <p>Scan QR code to pay</p>
                                    <p class="upi-instruction">Scan the QR using any UPI App</p>
                                    
                                    <div class="transaction-details">
                                        <div class="transaction-id">
                                            <span>Pay using any upi to make your order success ✓</span>
                                            <div class="txn-id">
                                                <span><?php echo esc_html($transaction_id); ?></span>
                                                <button class="copy-btn">Copy</button>
                                            </div>
                                        </div>
                                        
                                        <div class="amount-details">
                                            <div class="amount-row">
                                                <span>Amount to be Paid</span>
                                                <span class="amount-value"><?php echo iplpro_format_price($discounted_total); ?></span>
                                            </div>
                                        </div>
                                        
                                        <div class="utr-input">
                                            <label for="utr-number">UTR No/Transaction Number</label>
                                            <input type="text" id="utr-number" placeholder="Enter 12-digit UTR number">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="upi-app-icons">
                                    <div class="upi-app">
                                        <svg viewBox="0 0 24 24" width="40" height="40" class="phonepe">
                                            <circle cx="12" cy="12" r="12" fill="#5f259f" />
                                            <path d="M14,10.7V13.7C14,14.4 13.4,15 12.7,15H9.8V19H7V7H12.7C13.4,7 14,7.6 14,8.3V10.7M12,9H9.8V13H12V9Z" fill="#ffffff" />
                                        </svg>
                                        <span>PhonePe</span>
                                    </div>
                                    
                                    <div class="upi-app">
                                        <svg viewBox="0 0 24 24" width="40" height="40" class="paytm">
                                            <rect width="24" height="24" fill="#00baf2" />
                                            <path d="M7,7V17H9V13H11V17H13V7H11V11H9V7H7Z" fill="#ffffff" />
                                            <path d="M15,7V17H17V7H15Z" fill="#ffffff" />
                                        </svg>
                                        <span>PayTM</span>
                                    </div>
                                    
                                    <div class="upi-app">
                                        <svg viewBox="0 0 24 24" width="40" height="40" class="bhim">
                                            <rect width="24" height="24" fill="#00a0e4" />
                                            <path d="M6,6H18V10H6V6M6,11H18V18H6V11Z" fill="#ffffff" />
                                        </svg>
                                        <span>BHIM</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="cards-tab">
                            <div class="cards-content">
                                <div class="card-form">
                                    <div class="form-group">
                                        <label for="card-number">Card Number</label>
                                        <input type="text" id="card-number" placeholder="1234 5678 9012 3456" maxlength="19">
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group half">
                                            <label for="expiry-date">Expiry Date</label>
                                            <input type="text" id="expiry-date" placeholder="MM/YY" maxlength="5">
                                        </div>
                                        
                                        <div class="form-group half">
                                            <label for="cvv">CVV</label>
                                            <input type="text" id="cvv" placeholder="123" maxlength="3">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="card-name">Name on Card</label>
                                        <input type="text" id="card-name" placeholder="John Doe">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane" id="wallets-tab">
                            <div class="wallets-content">
                                <div class="wallet-options">
                                    <div class="wallet-option">
                                        <div class="wallet-icon">
                                            <svg viewBox="0 0 24 24" width="24" height="24">
                                                <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M12,6.5A5.5,5.5 0 0,1 17.5,12A5.5,5.5 0 0,1 12,17.5A5.5,5.5 0 0,1 6.5,12A5.5,5.5 0 0,1 12,6.5M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z" fill="#0080ff" />
                                            </svg>
                                        </div>
                                        <span class="wallet-name">Amazon Pay</span>
                                        <svg viewBox="0 0 24 24" width="16" height="16" class="chevron">
                                            <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" fill="currentColor" />
                                        </svg>
                                    </div>
                                    
                                    <div class="wallet-option">
                                        <div class="wallet-icon">
                                            <svg viewBox="0 0 24 24" width="24" height="24">
                                                <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,17H13V11H11V17Z" fill="#5f259f" />
                                                <path d="M11,9H13V7H11V9Z" fill="#5f259f" />
                                            </svg>
                                        </div>
                                        <span class="wallet-name">PhonePe Wallet</span>
                                        <svg viewBox="0 0 24 24" width="16" height="16" class="chevron">
                                            <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" fill="currentColor" />
                                        </svg>
                                    </div>
                                    
                                    <div class="wallet-option">
                                        <div class="wallet-icon">
                                            <svg viewBox="0 0 24 24" width="24" height="24">
                                                <path d="M19,13H5V11H19V13M12,5A2,2 0 0,1 14,7A2,2 0 0,1 12,9A2,2 0 0,1 10,7A2,2 0 0,1 12,5M12,11A4,4 0 0,0 16,7A4,4 0 0,0 12,3A4,4 0 0,0 8,7A4,4 0 0,0 12,11M12,13C7.58,13 4,15.79 4,19V21H20V19C20,15.79 16.42,13 12,13Z" fill="#ff0057" />
                                            </svg>
                                        </div>
                                        <span class="wallet-name">MobiKwik</span>
                                        <svg viewBox="0 0 24 24" width="16" height="16" class="chevron">
                                            <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" fill="currentColor" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="netbanking-section">
                    <button class="netbanking-btn">
                        <svg viewBox="0 0 24 24" width="20" height="20">
                            <path d="M6.5,10H4.5V17H6.5V10M12.5,10H10.5V17H12.5V10M21,19H2V21H21V19M18.5,10H16.5V17H18.5V10M11.5,3.26L16.71,6H6.29L11.5,3.26M11.5,1L2,6V8H21V6L11.5,1Z" fill="currentColor" />
                        </svg>
                        IMPS/NEFT
                        <svg viewBox="0 0 24 24" width="16" height="16" class="chevron">
                            <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z" fill="currentColor" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="payment-summary">
                <div class="summary-header">
                    <span class="summary-title">₹<?php echo number_format($price_details['total'], 2); ?></span>
                    <span class="discount">₹<?php echo number_format($discounted_total, 2); ?>(₹<?php echo number_format($discount, 2); ?> off)</span>
                </div>
                
                <button class="continue-btn">Continue</button>
            </div>
        </div>

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
