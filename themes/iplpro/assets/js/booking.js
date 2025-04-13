/**
 * Booking JavaScript for IPL Pro theme
 * Handles ticket booking, payment integration and form submission
 * 
 * @package iplpro
 */

(function() {
    'use strict';

    // Document ready
    document.addEventListener('DOMContentLoaded', function() {
        initBooking();
    });

    /**
     * Initialize booking functionality
     */
    function initBooking() {
        setupFormValidation();
        handlePaymentProcess();
        setupBackNavigation();
        setupStadiumSelection();
    }

    /**
     * Setup form validation for booking forms
     */
    function setupFormValidation() {
        const bookingForm = document.querySelector('.booking-form');
        
        if (!bookingForm) return;
        
        bookingForm.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = bookingForm.querySelectorAll('input[required], select[required], textarea[required]');
            
            // Clear previous errors
            bookingForm.querySelectorAll('.error-message').forEach(el => el.remove());
            
            // Validate each required field
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    showError(field, 'This field is required');
                } else if (field.type === 'email' && !validateEmail(field.value)) {
                    isValid = false;
                    showError(field, 'Please enter a valid email address');
                } else if (field.name === 'customer_phone' && !validatePhone(field.value)) {
                    isValid = false;
                    showError(field, 'Please enter a valid phone number');
                }
            });
            
            // Check if ticket type is selected
            const ticketTypeField = bookingForm.querySelector('input[name="ticket_type"]');
            if (ticketTypeField && !ticketTypeField.value) {
                isValid = false;
                const ticketSection = bookingForm.querySelector('.ticket-selection');
                if (ticketSection) {
                    showError(ticketSection, 'Please select a ticket type');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Email validation function
        function validateEmail(email) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(String(email).toLowerCase());
        }
        
        // Phone validation function
        function validatePhone(phone) {
            const re = /^[\d\+\-\(\) ]{8,15}$/;
            return re.test(String(phone));
        }
        
        // Show error message function
        function showError(element, message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            
            if (element.parentNode.classList.contains('form-field')) {
                element.parentNode.appendChild(errorDiv);
            } else {
                element.parentNode.insertBefore(errorDiv, element.nextSibling);
            }
            
            element.classList.add('error');
            
            // Remove error on input
            element.addEventListener('input', function() {
                removeError(element);
            });
        }
        
        // Remove error message function
        function removeError(element) {
            const errorDiv = element.parentNode.querySelector('.error-message');
            if (errorDiv) {
                errorDiv.remove();
            }
            element.classList.remove('error');
        }
    }

    /**
     * Handle Razorpay payment integration
     */
    function handlePaymentProcess() {
        const utrForm = document.querySelector('.utr-form');
        
        if (!utrForm) return;
        
        utrForm.addEventListener('submit', function(e) {
            // Validate UTR number
            const utrField = document.getElementById('utr_number');
            if (utrField && !validateUTR(utrField.value)) {
                e.preventDefault();
                showPaymentError('Please enter a valid UTR number (12-22 characters)');
                utrField.focus();
            }
        });
        
        // UTR validation function
        function validateUTR(utr) {
            return /^[a-zA-Z0-9]{12,22}$/.test(utr);
        }
        
        // Show payment error function
        function showPaymentError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            
            const utrField = document.getElementById('utr_number');
            if (utrField && utrField.parentNode) {
                if (utrField.parentNode.querySelector('.error-message')) {
                    utrField.parentNode.querySelector('.error-message').remove();
                }
                utrField.parentNode.appendChild(errorDiv);
            }
        }
        
        // Handle app redirect buttons
        const appButtons = document.querySelectorAll('.upi-app-button');
        
        appButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Store payment attempt information
                const orderId = new URLSearchParams(window.location.search).get('order_id');
                if (orderId) {
                    localStorage.setItem('payment_initiated', orderId);
                    localStorage.setItem('payment_time', new Date().toISOString());
                }
            });
        });
        
        // Check for URL params when returning from payment app
        window.addEventListener('load', function() {
            const paymentData = getPaymentDataFromURL();
            if (paymentData) {
                completePayment(paymentData);
            }
        });
        
        // Add query parameters to URL 
        function addQueryParams(url, params) {
            const urlObj = new URL(url, window.location.origin);
            Object.keys(params).forEach(key => {
                urlObj.searchParams.append(key, params[key]);
            });
            return urlObj.toString();
        }
        
        // Get payment data from URL
        function getPaymentDataFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            const txnId = urlParams.get('txnId');
            const paymentStatus = urlParams.get('Status') || urlParams.get('status');
            
            if (txnId && paymentStatus) {
                return {
                    txnId: txnId,
                    status: paymentStatus
                };
            }
            
            return null;
        }
        
        // Complete payment process
        function completePayment(paymentData, referenceId) {
            const orderId = new URLSearchParams(window.location.search).get('order_id') || localStorage.getItem('payment_initiated');
            
            if (!orderId) return;
            
            // Fill UTR field if available
            const utrField = document.getElementById('utr_number');
            if (utrField && paymentData.txnId) {
                utrField.value = paymentData.txnId;
            }
            
            // Clear local storage
            localStorage.removeItem('payment_initiated');
            localStorage.removeItem('payment_time');
        }
    }

    /**
     * Setup back navigation functionality
     */
    function setupBackNavigation() {
        const backButtons = document.querySelectorAll('.back-btn, .back-link');
        
        backButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                window.history.back();
            });
        });
    }

    /**
     * Handle stadium map section selection
     */
    function setupStadiumSelection() {
        const mapSections = document.querySelectorAll('.map-section');
        
        if (!mapSections.length) return;
        
        mapSections.forEach(section => {
            section.addEventListener('click', function() {
                const sectionName = this.getAttribute('data-section');
                const sectionPrice = this.getAttribute('data-price');
                
                // Map section to ticket type
                mapSectionToTicketType(sectionName);
                
                // Show selected section message
                showSelectedSection(sectionName);
            });
        });
        
        function mapSectionToTicketType(section) {
            const ticketCards = document.querySelectorAll('.ticket-type-card');
            
            let foundCard = false;
            
            ticketCards.forEach(card => {
                const cardType = card.querySelector('.ticket-type-name').textContent.trim();
                
                if (cardType === section) {
                    card.click();
                    foundCard = true;
                }
            });
            
            // If no matching card found, select first one
            if (!foundCard && ticketCards.length > 0) {
                ticketCards[0].click();
            }
        }
        
        function showSelectedSection(section) {
            const selectionMessage = document.querySelector('.selection-message');
            
            if (selectionMessage) {
                selectionMessage.innerHTML = 'You selected <strong>' + section + '</strong>';
                selectionMessage.style.display = 'block';
            }
        }
    }
})();