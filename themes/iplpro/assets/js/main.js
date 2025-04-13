/**
 * Main JavaScript file for IPL Pro theme
 * 
 * @package iplpro
 */

(function() {
    'use strict';

    // Document ready
    document.addEventListener('DOMContentLoaded', function() {
        initTheme();
    });

    /**
     * Initialize the theme
     */
    function initTheme() {
        setupMobileMenu();
        setupHeroSlider();
        initLazyLoading();
        setupScrollEffects();
        setupTabNavigation();
        setupQuantityControl();
        setupTicketTypeSelection();
        setupPaymentTabs();
        setupCopyToClipboard();
    }

    /**
     * Setup mobile menu functionality
     */
    function setupMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNavigation = document.querySelector('.main-navigation');
        
        if (!menuToggle || !mainNavigation) return;
        
        menuToggle.addEventListener('click', function() {
            mainNavigation.classList.toggle('active');
            this.setAttribute('aria-expanded', this.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');
        });
    }

    /**
     * Setup hero slider functionality
     */
    function setupHeroSlider() {
        const slider = document.querySelector('.hero-slider');
        
        if (!slider) return;
        
        const slides = slider.querySelectorAll('.hero-slide');
        const dots = slider.querySelectorAll('.slider-dot');
        let currentSlide = 0;
        let slideInterval;
        
        // Start auto slideshow
        startAutoSlide();
        
        // Handle dot clicks
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
                restartAutoSlide();
            });
        });
        
        // Auto slide function
        function startAutoSlide() {
            slideInterval = setInterval(() => {
                nextSlide();
            }, 5000);
        }
        
        // Restart auto slide
        function restartAutoSlide() {
            clearInterval(slideInterval);
            startAutoSlide();
        }
        
        // Next slide function
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }
        
        // Show specific slide
        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            slides[index].classList.add('active');
            dots[index].classList.add('active');
        }
        
        // Handle swipe events for mobile
        handleSwipe(slider, () => {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
            restartAutoSlide();
        }, () => {
            nextSlide();
            restartAutoSlide();
        });
        
        // Swipe detection
        function handleSwipe(element, onSwipeLeft, onSwipeRight) {
            let touchStartX = 0;
            let touchEndX = 0;
            
            element.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            }, false);
            
            element.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipeGesture();
            }, false);
            
            function handleSwipeGesture() {
                const swipeThreshold = 50;
                if (touchEndX < touchStartX - swipeThreshold) {
                    // Swiped left
                    if (onSwipeLeft) onSwipeLeft();
                } else if (touchEndX > touchStartX + swipeThreshold) {
                    // Swiped right
                    if (onSwipeRight) onSwipeRight();
                }
            }
        }
    }

    /**
     * Initialize lazy loading images
     */
    function initLazyLoading() {
        const lazyImages = document.querySelectorAll('img[data-src]');
        
        if (!lazyImages.length) return;
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => {
            imageObserver.observe(img);
        });
    }

    /**
     * Setup scroll effects
     */
    function setupScrollEffects() {
        const header = document.querySelector('.site-header');
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Header shrink effect
            if (header && scrollTop > 100) {
                header.classList.add('scrolled');
            } else if (header) {
                header.classList.remove('scrolled');
            }
            
            lastScrollTop = scrollTop;
        });
    }

    /**
     * Handle tab navigation for ticket types and stadium map
     */
    function setupTabNavigation() {
        const tabButtons = document.querySelectorAll('.tab-button');
        
        if (!tabButtons.length) return;
        
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                const tabContent = document.querySelector(`.tab-content[data-tab="${tabId}"]`);
                
                // Remove active class from all tabs and contents
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                // Add active class to current tab and content
                button.classList.add('active');
                if (tabContent) tabContent.classList.add('active');
            });
        });
    }

    /**
     * Handle quantity control in ticket selection
     */
    function setupQuantityControl() {
        const quantityControls = document.querySelectorAll('.quantity-control');
        
        if (!quantityControls.length) return;
        
        quantityControls.forEach(control => {
            const minusBtn = control.querySelector('.minus');
            const plusBtn = control.querySelector('.plus');
            const input = control.querySelector('input');
            
            if (!minusBtn || !plusBtn || !input) return;
            
            minusBtn.addEventListener('click', () => {
                let value = parseInt(input.value, 10);
                value = Math.max(parseInt(input.min, 10) || 1, value - 1);
                input.value = value;
                input.dispatchEvent(new Event('change'));
            });
            
            plusBtn.addEventListener('click', () => {
                let value = parseInt(input.value, 10);
                const max = parseInt(input.max, 10) || 10;
                value = Math.min(max, value + 1);
                input.value = value;
                input.dispatchEvent(new Event('change'));
            });
            
            input.addEventListener('change', () => {
                updateTotal();
            });
            
            // Update total on load
            updateTotal();
        });
        
        function updateTotal() {
            const ticketPriceElement = document.querySelector('.ticket-price');
            const quantityInput = document.querySelector('.quantity-control input');
            const totalElement = document.querySelector('.total-amount');
            
            if (!ticketPriceElement || !quantityInput || !totalElement) return;
            
            const price = parseFloat(ticketPriceElement.dataset.price || ticketPriceElement.textContent.replace(/[^0-9.]/g, ''));
            const quantity = parseInt(quantityInput.value, 10);
            
            if (isNaN(price) || isNaN(quantity)) return;
            
            const total = price * quantity;
            totalElement.textContent = '₹' + total.toFixed(2);
            
            // Update hidden field for form submission
            const quantityField = document.querySelector('input[name="quantity"]');
            if (quantityField) {
                quantityField.value = quantity;
            }
        }
    }

    /**
     * Handle ticket type selection
     */
    function setupTicketTypeSelection() {
        const ticketCards = document.querySelectorAll('.ticket-type-card');
        const ticketTypeField = document.querySelector('input[name="ticket_type"]');
        
        if (!ticketCards.length) return;
        
        ticketCards.forEach(card => {
            card.addEventListener('click', () => {
                // Remove selected class from all cards
                ticketCards.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked card
                card.classList.add('selected');
                
                // Update selected ticket type display
                const ticketType = card.getAttribute('data-ticket-type');
                const ticketPrice = card.getAttribute('data-price');
                const selectedTypeElement = document.querySelector('.selected-ticket-type');
                const ticketPriceElement = document.querySelector('.ticket-price');
                
                if (selectedTypeElement) {
                    selectedTypeElement.textContent = ticketType;
                }
                
                if (ticketPriceElement && ticketPrice) {
                    ticketPriceElement.textContent = '₹' + ticketPrice;
                    ticketPriceElement.dataset.price = ticketPrice;
                    
                    // Update total by triggering change on quantity
                    const quantityInput = document.querySelector('.quantity-control input');
                    if (quantityInput) {
                        quantityInput.dispatchEvent(new Event('change'));
                    }
                }
                
                // Update hidden field for form submission
                if (ticketTypeField) {
                    ticketTypeField.value = ticketType;
                }
            });
        });
        
        // Select first ticket by default
        if (ticketCards.length > 0 && !document.querySelector('.ticket-type-card.selected')) {
            ticketCards[0].click();
        }
    }

    /**
     * Initialize payment tabs
     */
    function setupPaymentTabs() {
        const paymentTabs = document.querySelectorAll('.payment-tab');
        
        if (!paymentTabs.length) return;
        
        paymentTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab');
                
                // Remove active class from all tabs and panes
                document.querySelectorAll('.payment-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                
                // Add active class to current tab and pane
                tab.classList.add('active');
                document.getElementById(tabId + '-tab').classList.add('active');
            });
        });
    }

    /**
     * Copy to clipboard functionality
     */
    function setupCopyToClipboard() {
        const copyButtons = document.querySelectorAll('.copy-btn');
        
        if (!copyButtons.length) return;
        
        copyButtons.forEach(button => {
            button.addEventListener('click', () => {
                const textToCopy = button.getAttribute('data-copy');
                const originalText = button.textContent;
                
                navigator.clipboard.writeText(textToCopy).then(() => {
                    button.textContent = 'Copied!';
                    setTimeout(() => {
                        button.textContent = originalText;
                    }, 2000);
                }).catch(err => {
                    console.error('Could not copy text: ', err);
                });
            });
        });
    }
})();