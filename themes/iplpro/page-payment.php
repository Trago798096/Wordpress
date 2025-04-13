<?php
/**
 * Template for displaying payment page
 *
 * @package iplpro
 */

// Check if we have order ID in query string or POST
if (!isset($_GET['order_id']) && !isset($_POST['order_id'])) {
    // If not, redirect to homepage
    wp_redirect(home_url());
    exit;
}

// Get order ID
$order_id = isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : sanitize_text_field($_POST['order_id']);

// Check if payment was saved in session
if (!session_id()) {
    session_start();
}

// Check for order in session
if (isset($_SESSION['iplpro_order']) && $_SESSION['iplpro_order']['order_id'] === $order_id) {
    $order_data = $_SESSION['iplpro_order'];
} else {
    // Try to get order from database
    $order_posts = get_posts(array(
        'post_type' => 'order',
        'meta_key' => 'order_id',
        'meta_value' => $order_id,
        'posts_per_page' => 1
    ));
    
    if (empty($order_posts)) {
        wp_redirect(home_url());
        exit;
    }
    
    $order_post_id = $order_posts[0]->ID;
    
    // Get order data
    $order_data = array(
        'order_id' => $order_id,
        'order_post_id' => $order_post_id,
        'match_title' => get_field('match_title', $order_post_id),
        'ticket_type' => get_field('ticket_type', $order_post_id),
        'quantity' => get_field('quantity', $order_post_id),
        'price_per_ticket' => get_field('price_per_ticket', $order_post_id),
        'total_amount' => get_field('total_amount', $order_post_id),
        'customer_name' => get_field('customer_name', $order_post_id),
        'customer_email' => get_field('customer_email', $order_post_id),
        'customer_phone' => get_field('customer_phone', $order_post_id)
    );
}

// Check for payment error
$payment_error = isset($_GET['payment_error']) ? urldecode($_GET['payment_error']) : '';

// Generate Razorpay order if needed
$razorpay_order_id = iplpro_generate_razorpay_order($order_id, $order_data['total_amount']);

// Calculate expiry time (15 minutes from now)
$expiry_time = time() + (15 * 60);
$minutes_remaining = 15;

get_header();
?>

<main id="primary" class="site-main">
    <div class="container">
        <h1 class="payment-page-title"><?php echo esc_html__('Complete Your Payment', 'iplpro'); ?></h1>
        
        <div class="payment-options">
            <div class="payment-header">
                <h2><?php echo esc_html__('Order Summary', 'iplpro'); ?></h2>
                <div class="order-details">
                    <p><strong><?php echo esc_html__('Order ID:', 'iplpro'); ?></strong> <?php echo esc_html($order_id); ?></p>
                    <p><strong><?php echo esc_html__('Match:', 'iplpro'); ?></strong> <?php echo esc_html($order_data['match_title']); ?></p>
                    <p><strong><?php echo esc_html__('Tickets:', 'iplpro'); ?></strong> <?php echo esc_html($order_data['quantity']) . ' x ' . esc_html($order_data['ticket_type']); ?></p>
                    <p><strong><?php echo esc_html__('Total Amount:', 'iplpro'); ?></strong> ₹<?php echo esc_html(number_format($order_data['total_amount'])); ?></p>
                </div>
                
                <div class="time-remaining">
                    <span class="dashicons dashicons-clock"></span>
                    <span class="timer-text"><?php echo esc_html__('Time Remaining:', 'iplpro'); ?> <span id="countdown"><?php echo esc_html($minutes_remaining); ?>:00</span></span>
                </div>
                
                <?php if (!empty($payment_error)): ?>
                <div class="payment-error">
                    <p><?php echo esc_html($payment_error); ?></p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="payment-tabs">
                <div class="payment-tab active" data-tab="upi"><?php echo esc_html__('UPI Payment', 'iplpro'); ?></div>
                <div class="payment-tab" data-tab="card"><?php echo esc_html__('Credit/Debit Card', 'iplpro'); ?></div>
                <div class="payment-tab" data-tab="bank"><?php echo esc_html__('Net Banking', 'iplpro'); ?></div>
            </div>
            
            <div class="tab-pane active" id="upi-tab">
                <div class="upi-payment">
                    <div class="qr-code-container">
                        <h4><?php echo esc_html__('Scan QR Code', 'iplpro'); ?></h4>
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/upi-qr.png'); ?>" alt="<?php echo esc_attr__('UPI QR Code', 'iplpro'); ?>" class="qr-code-image">
                    </div>
                    
                    <div class="upi-id-container">
                        <h4><?php echo esc_html__('or Pay using UPI ID', 'iplpro'); ?></h4>
                        <div class="upi-id">ipl@ybl</div>
                        <button class="copy-btn" data-clipboard-text="ipl@ybl">
                            <span class="dashicons dashicons-clipboard"></span>
                            <?php echo esc_html__('Copy', 'iplpro'); ?>
                        </button>
                    </div>
                    
                    <div class="upi-app-buttons">
                        <a href="#" class="upi-app-button">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/gpay.png'); ?>" alt="Google Pay" class="app-icon">
                            <span class="app-name">Google Pay</span>
                        </a>
                        
                        <a href="#" class="upi-app-button">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/phonepe.png'); ?>" alt="PhonePe" class="app-icon">
                            <span class="app-name">PhonePe</span>
                        </a>
                        
                        <a href="#" class="upi-app-button">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/paytm.png'); ?>" alt="Paytm" class="app-icon">
                            <span class="app-name">Paytm</span>
                        </a>
                    </div>
                    
                    <div class="order-reference">
                        <div class="reference-label"><?php echo esc_html__('Use this Reference ID for payment', 'iplpro'); ?></div>
                        <div class="reference-value"><?php echo esc_html($order_id); ?></div>
                    </div>
                    
                    <form action="" method="post" class="utr-form">
                        <div class="form-instructions">
                            <?php echo esc_html__('After completing the payment, please enter the UTR Number / Transaction ID below for verification.', 'iplpro'); ?>
                        </div>
                        
                        <div class="utr-field">
                            <label for="utr_number"><?php echo esc_html__('UTR Number / Transaction ID', 'iplpro'); ?></label>
                            <input type="text" name="utr_number" id="utr_number" required>
                        </div>
                        
                        <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
                        <input type="hidden" name="payment_method" value="upi">
                        
                        <button type="submit" class="utr-submit-btn"><?php echo esc_html__('Submit UTR Number', 'iplpro'); ?></button>
                    </form>
                </div>
            </div>
            
            <div class="tab-pane" id="card-tab">
                <div class="card-payment">
                    <div class="razorpay-container">
                        <h4><?php echo esc_html__('Pay with Credit/Debit Card', 'iplpro'); ?></h4>
                        
                        <div class="razorpay-button-container">
                            <button class="razorpay-btn" id="razorpay-button">
                                <?php echo esc_html__('Pay Now', 'iplpro'); ?> ₹<?php echo esc_html(number_format($order_data['total_amount'])); ?>
                            </button>
                        </div>
                        
                        <div class="card-logos">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/visa.png'); ?>" alt="Visa" class="card-logo">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/mastercard.png'); ?>" alt="Mastercard" class="card-logo">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/rupay.png'); ?>" alt="RuPay" class="card-logo">
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-pane" id="bank-tab">
                <div class="bank-payment">
                    <h4><?php echo esc_html__('Bank Transfer Details', 'iplpro'); ?></h4>
                    
                    <div class="bank-details">
                        <div class="bank-detail-item">
                            <span class="bank-detail-label"><?php echo esc_html__('Account Name:', 'iplpro'); ?></span>
                            <span class="bank-detail-value">IPL Tickets Pvt Ltd</span>
                        </div>
                        
                        <div class="bank-detail-item">
                            <span class="bank-detail-label"><?php echo esc_html__('Account Number:', 'iplpro'); ?></span>
                            <span class="bank-detail-value">1234 5678 9012 3456</span>
                        </div>
                        
                        <div class="bank-detail-item">
                            <span class="bank-detail-label"><?php echo esc_html__('IFSC Code:', 'iplpro'); ?></span>
                            <span class="bank-detail-value">HDFC0001234</span>
                        </div>
                        
                        <div class="bank-detail-item">
                            <span class="bank-detail-label"><?php echo esc_html__('Bank:', 'iplpro'); ?></span>
                            <span class="bank-detail-value">HDFC Bank</span>
                        </div>
                        
                        <div class="bank-detail-item">
                            <span class="bank-detail-label"><?php echo esc_html__('Branch:', 'iplpro'); ?></span>
                            <span class="bank-detail-value">Mumbai Main Branch</span>
                        </div>
                    </div>
                    
                    <div class="order-reference">
                        <div class="reference-label"><?php echo esc_html__('Use this Reference ID for payment', 'iplpro'); ?></div>
                        <div class="reference-value"><?php echo esc_html($order_id); ?></div>
                    </div>
                    
                    <form action="" method="post" class="bank-form">
                        <div class="form-instructions">
                            <?php echo esc_html__('After completing the bank transfer, please enter the transaction reference number below for verification.', 'iplpro'); ?>
                        </div>
                        
                        <div class="utr-field">
                            <label for="transfer_reference"><?php echo esc_html__('Transaction Reference', 'iplpro'); ?></label>
                            <input type="text" name="transfer_reference" id="transfer_reference" required>
                        </div>
                        
                        <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
                        <input type="hidden" name="payment_method" value="bank_transfer">
                        
                        <button type="submit" class="utr-submit-btn"><?php echo esc_html__('Submit Reference', 'iplpro'); ?></button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="payment-notice">
            <p><?php echo esc_html__('Note: Please complete the payment within 15 minutes. After this time, your booking will be cancelled.', 'iplpro'); ?></p>
            <p><a href="javascript:history.back();" class="back-btn"><?php echo esc_html__('Back to Booking Summary', 'iplpro'); ?></a></p>
        </div>
    </div>
</main>

<!-- Razorpay Integration -->
<form id="razorpay-hidden-form" action="" method="post">
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">
    <input type="hidden" name="payment_method" value="razorpay">
</form>

<script>
    // Countdown timer
    document.addEventListener('DOMContentLoaded', function() {
        var countdownElement = document.getElementById('countdown');
        var minutes = <?php echo esc_js($minutes_remaining); ?>;
        var seconds = 0;
        
        var countdownInterval = setInterval(function() {
            seconds--;
            
            if (seconds < 0) {
                minutes--;
                seconds = 59;
            }
            
            if (minutes < 0) {
                clearInterval(countdownInterval);
                window.location.href = '<?php echo esc_url(home_url()); ?>';
                return;
            }
            
            countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
        }, 1000);
        
        // Payment tabs
        var tabs = document.querySelectorAll('.payment-tab');
        var panes = document.querySelectorAll('.tab-pane');
        
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                var tabId = this.getAttribute('data-tab');
                
                tabs.forEach(function(t) {
                    t.classList.remove('active');
                });
                
                panes.forEach(function(pane) {
                    pane.classList.remove('active');
                });
                
                this.classList.add('active');
                document.getElementById(tabId + '-tab').classList.add('active');
            });
        });
        
        // Copy to clipboard
        var copyBtn = document.querySelector('.copy-btn');
        if (copyBtn) {
            copyBtn.addEventListener('click', function() {
                var text = this.getAttribute('data-clipboard-text');
                var tempInput = document.createElement('input');
                tempInput.value = text;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                
                var originalText = this.innerHTML;
                this.innerHTML = '<span class="dashicons dashicons-yes"></span> Copied!';
                
                setTimeout(function() {
                    copyBtn.innerHTML = originalText;
                }, 2000);
            });
        }
        
        // Razorpay integration
        var razorpayBtn = document.getElementById('razorpay-button');
        if (razorpayBtn) {
            razorpayBtn.addEventListener('click', function() {
                var options = {
                    key: 'rzp_test_your_key_here', // Replace with your key
                    amount: <?php echo esc_js($order_data['total_amount'] * 100); ?>, // Amount in paise
                    currency: 'INR',
                    name: 'IPL Tickets',
                    description: '<?php echo esc_js($order_data['match_title']); ?>',
                    order_id: '<?php echo esc_js($razorpay_order_id); ?>',
                    handler: function(response) {
                        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                        document.getElementById('razorpay-hidden-form').submit();
                    },
                    prefill: {
                        name: '<?php echo esc_js($order_data['customer_name']); ?>',
                        email: '<?php echo esc_js($order_data['customer_email']); ?>',
                        contact: '<?php echo esc_js($order_data['customer_phone']); ?>'
                    },
                    theme: {
                        color: '#FF4F00'
                    }
                };
                
                var rzp = new Razorpay(options);
                rzp.open();
            });
        }
    });
</script>

<?php
get_footer();