/**
 * Booking JavaScript for IPL Pro theme
 * Handles ticket booking, payment integration and form submission
 * 
 * @package iplpro
 */

(function($) {
    'use strict';
    
    /**
     * Initialize booking functionality
     */
    function initBooking() {
        setupFormValidation();
        handlePaymentProcess();
        setupBackNavigation();
    }
    
    /**
     * Setup form validation for booking forms
     */
    function setupFormValidation() {
        const $customerInfoForm = $('#customer-info-form');
        
        if ($customerInfoForm.length === 0) {
            return;
        }
        
        $customerInfoForm.on('submit', function(e) {
            const $fullname = $('#fullname');
            const $email = $('#email');
            const $phone = $('#phone');
            
            let isValid = true;
            
            // Validate fullname
            if ($fullname.val().trim() === '') {
                showError($fullname, 'Please enter your full name');
                isValid = false;
            } else {
                removeError($fullname);
            }
            
            // Validate email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if ($email.val().trim() === '' || !emailRegex.test($email.val().trim())) {
                showError($email, 'Please enter a valid email address');
                isValid = false;
            } else {
                removeError($email);
            }
            
            // Validate phone
            const phoneRegex = /^[0-9]{10}$/;
            if ($phone.val().trim() === '' || !phoneRegex.test($phone.val().trim())) {
                showError($phone, 'Please enter a valid 10-digit phone number');
                isValid = false;
            } else {
                removeError($phone);
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        function showError($element, message) {
            // Remove any existing error
            removeError($element);
            
            // Add error class and message
            $element.addClass('error');
            $element.after('<span class="error-message">' + message + '</span>');
        }
        
        function removeError($element) {
            $element.removeClass('error');
            $element.next('.error-message').remove();
        }
    }
    
    /**
     * Handle Razorpay payment integration
     */
    function handlePaymentProcess() {
        const $paymentBtn = $('.payment-btn');
        const $payNowBtn = $('.continue-btn');
        
        if ($paymentBtn.length) {
            $paymentBtn.on('click', function(e) {
                e.preventDefault();
                
                const $form = $(this).closest('form');
                const formValid = validateForm($form);
                
                if (!formValid) {
                    return;
                }
                
                // Collect booking data
                const bookingData = {
                    match_id: $form.find('input[name="match_id"]').val(),
                    seat_type: $form.find('input[name="seat_type"]').val(),
                    quantity: $form.find('input[name="quantity"]').val(),
                    total: $form.find('input[name="total"]').val(),
                    fullname: $form.find('input[name="fullname"]').val(),
                    email: $form.find('input[name="email"]').val(),
                    phone: $form.find('input[name="phone"]').val(),
                    nonce: iplpro_booking.nonce,
                    action: 'iplpro_create_razorpay_payment'
                };
                
                // Show loading state
                $(this).prop('disabled', true).text('Processing...');
                
                // Make AJAX request to create Razorpay order
                $.ajax({
                    url: iplpro_booking.ajax_url,
                    type: 'POST',
                    data: bookingData,
                    success: function(response) {
                        if (response.success) {
                            // Redirect to payment page
                            window.location.href = addQueryParams(
                                window.location.origin + '/payment',
                                bookingData
                            );
                        } else {
                            showPaymentError(response.data.message || 'An error occurred. Please try again.');
                            $paymentBtn.prop('disabled', false).text('Pay with Razorpay ₹' + bookingData.total);
                        }
                    },
                    error: function() {
                        showPaymentError('Network error. Please check your connection and try again.');
                        $paymentBtn.prop('disabled', false).text('Pay with Razorpay ₹' + bookingData.total);
                    }
                });
            });
        }
        
        if ($payNowBtn.length) {
            $payNowBtn.on('click', function() {
                // Simulate successful payment (in a real scenario, this would handle actual payment)
                const utrNumber = $('#utr-number').val();
                
                if (utrNumber && utrNumber.length >= 10) {
                    // If UTR number is provided, proceed with payment confirmation
                    const paymentData = getPaymentDataFromURL();
                    completePayment(paymentData, utrNumber);
                } else if ($('#card-number').val() && $('#card-number').val().length >= 16) {
                    // If card details are provided
                    const paymentData = getPaymentDataFromURL();
                    completePayment(paymentData, 'card_' + Math.random().toString(36).substring(2, 10));
                } else {
                    // Show error if no payment method details provided
                    alert('Please provide payment details to continue');
                }
            });
        }
        
        function validateForm($form) {
            let isValid = true;
            
            $form.find('input[required]').each(function() {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                }
            });
            
            return isValid;
        }
        
        function showPaymentError(message) {
            if ($('.payment-error').length === 0) {
                $('<div class="payment-error"></div>').insertBefore($paymentBtn);
            }
            
            $('.payment-error').html('<p>' + message + '</p>');
        }
        
        function addQueryParams(url, params) {
            const urlObj = new URL(url);
            
            Object.keys(params).forEach(key => {
                if (key !== 'action' && key !== 'nonce') {
                    urlObj.searchParams.set(key, params[key]);
                }
            });
            
            return urlObj.toString();
        }
        
        function getPaymentDataFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            
            return {
                match_id: urlParams.get('match_id'),
                seat_type: urlParams.get('seat_type'),
                quantity: urlParams.get('quantity'),
                fullname: urlParams.get('fullname'),
                email: urlParams.get('email'),
                phone: urlParams.get('phone'),
                total: urlParams.get('total')
            };
        }
        
        function completePayment(paymentData, referenceId) {
            // In a real scenario, this would handle the Razorpay callback
            // For this implementation, we'll simulate a successful payment
            
            // Add razorpay parameters that would come from a real payment
            const redirectParams = {
                ...paymentData,
                razorpay_payment_id: 'pay_' + Math.random().toString(36).substring(2, 15),
                razorpay_order_id: 'order_' + Math.random().toString(36).substring(2, 15),
                razorpay_signature: 'signature_' + Math.random().toString(36).substring(2, 30)
            };
            
            // Redirect to booking-confirmed page
            window.location.href = addQueryParams(
                window.location.origin + '/booking-confirmed',
                redirectParams
            );
        }
    }
    
    /**
     * Setup back navigation functionality
     */
    function setupBackNavigation() {
        $('.back-link').on('click', function(e) {
            // Check if we have previous history
            if (window.history.length > 1) {
                e.preventDefault();
                window.history.back();
            }
        });
    }
    
    /**
     * Handle stadium map section selection
     */
    function setupStadiumSelection() {
        $('.stadium-map path, .stadium-map .section').on('click', function() {
            const section = $(this).data('section');
            
            if (section) {
                // Find corresponding ticket type
                const ticketType = mapSectionToTicketType(section);
                
                // Update selection
                $('.ticket-type-option').removeClass('selected');
                $('.ticket-type-option[data-type="' + ticketType + '"]').addClass('selected');
                
                // Update form field
                $('input[name="seat_type"]').val(ticketType);
                
                // Show selected section feedback
                showSelectedSection(section);
            }
        });
        
        function mapSectionToTicketType(section) {
            // Map section names to ticket types
            const sectionMap = {
                'VIP': 'VIP Stand',
                'Premium': 'Premium Stand',
                'Pavilion': 'Pavilion Stand',
                'General': 'General Stand',
                'Corporate': 'Corporate Box'
            };
            
            // Check if section contains one of the key words
            for (const key in sectionMap) {
                if (section.includes(key)) {
                    return sectionMap[key];
                }
            }
            
            // Default to General Stand
            return 'General Stand';
        }
        
        function showSelectedSection(section) {
            // Show toast notification
            if ($('.section-toast').length === 0) {
                $('<div class="section-toast"></div>').appendTo('body');
            }
            
            $('.section-toast').text('Selected: ' + section).addClass('show');
            
            setTimeout(function() {
                $('.section-toast').removeClass('show');
            }, 2000);
        }
    }
    
    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        initBooking();
        
        if ($('.stadium-map').length) {
            setupStadiumSelection();
        }
    });
    
})(jQuery);
