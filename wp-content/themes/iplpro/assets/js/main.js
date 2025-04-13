/**
 * Main JavaScript file for IPL Pro theme
 * 
 * @package iplpro
 */

(function($) {
    'use strict';
    
    /**
     * Initialize the theme
     */
    function initTheme() {
        setupMobileMenu();
        setupHeroSlider();
        initLazyLoading();
        setupScrollEffects();
    }
    
    /**
     * Setup mobile menu functionality
     */
    function setupMobileMenu() {
        const $menuToggle = $('.menu-toggle');
        const $mainNavigation = $('.main-navigation');
        const $siteNavigation = $('#site-navigation');
        
        $menuToggle.on('click', function() {
            $(this).toggleClass('active');
            $mainNavigation.toggleClass('toggled');
            
            if ($mainNavigation.hasClass('toggled')) {
                $siteNavigation.attr('aria-expanded', 'true');
            } else {
                $siteNavigation.attr('aria-expanded', 'false');
            }
        });
        
        // Close menu when clicking outside
        $(document).on('click', function(event) {
            if ($mainNavigation.hasClass('toggled') && 
                !$(event.target).closest('.main-navigation').length && 
                !$(event.target).closest('.menu-toggle').length) {
                $menuToggle.removeClass('active');
                $mainNavigation.removeClass('toggled');
                $siteNavigation.attr('aria-expanded', 'false');
            }
        });
        
        // Handle submenu toggles
        $('.menu-item-has-children > a').after('<button class="submenu-toggle"><span class="screen-reader-text">Toggle submenu</span></button>');
        
        $('.submenu-toggle').on('click', function(e) {
            e.preventDefault();
            $(this).toggleClass('active');
            $(this).next('.sub-menu').toggleClass('toggled');
        });
    }
    
    /**
     * Setup hero slider functionality
     */
    function setupHeroSlider() {
        const $sliderContainer = $('.slider-container');
        const $sliderItems = $('.slider-item');
        const $sliderDots = $('.slider-dot');
        
        if ($sliderContainer.length === 0) {
            return;
        }
        
        let currentSlide = 0;
        const slideCount = $sliderItems.length;
        
        if (slideCount <= 1) {
            return;
        }
        
        // Setup auto-rotate
        let sliderInterval = setInterval(nextSlide, 5000);
        
        function nextSlide() {
            showSlide((currentSlide + 1) % slideCount);
        }
        
        function showSlide(index) {
            $sliderItems.removeClass('active');
            $sliderDots.removeClass('active');
            
            $sliderItems.eq(index).addClass('active');
            $sliderDots.eq(index).addClass('active');
            
            currentSlide = index;
        }
        
        // Handle dot navigation
        $sliderDots.on('click', function() {
            const index = $(this).data('slide');
            showSlide(index);
            
            // Reset interval when manually changing slide
            clearInterval(sliderInterval);
            sliderInterval = setInterval(nextSlide, 5000);
        });
        
        // Handle swipe gestures
        let touchStartX = 0;
        let touchEndX = 0;
        
        $sliderContainer.on('touchstart', function(e) {
            touchStartX = e.originalEvent.touches[0].clientX;
        });
        
        $sliderContainer.on('touchend', function(e) {
            touchEndX = e.originalEvent.changedTouches[0].clientX;
            handleSwipe();
            
            // Reset interval when manually changing slide
            clearInterval(sliderInterval);
            sliderInterval = setInterval(nextSlide, 5000);
        });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            
            if (touchEndX < touchStartX - swipeThreshold) {
                // Swipe left, go to next slide
                nextSlide();
            } else if (touchEndX > touchStartX + swipeThreshold) {
                // Swipe right, go to previous slide
                showSlide((currentSlide - 1 + slideCount) % slideCount);
            }
        }
    }
    
    /**
     * Initialize lazy loading images
     */
    function initLazyLoading() {
        // Check if Intersection Observer is supported
        if ('IntersectionObserver' in window) {
            const lazyImages = document.querySelectorAll('.lazy-load');
            
            const imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;
                        
                        if (lazyImage.dataset.srcset) {
                            lazyImage.srcset = lazyImage.dataset.srcset;
                        }
                        
                        lazyImage.classList.remove('lazy-load');
                        imageObserver.unobserve(lazyImage);
                    }
                });
            });
            
            lazyImages.forEach(function(lazyImage) {
                imageObserver.observe(lazyImage);
            });
        } else {
            // Fallback for browsers that don't support Intersection Observer
            const lazyImages = document.querySelectorAll('.lazy-load');
            
            lazyImages.forEach(function(lazyImage) {
                lazyImage.src = lazyImage.dataset.src;
                
                if (lazyImage.dataset.srcset) {
                    lazyImage.srcset = lazyImage.dataset.srcset;
                }
                
                lazyImage.classList.remove('lazy-load');
            });
        }
    }
    
    /**
     * Setup scroll effects
     */
    function setupScrollEffects() {
        const $header = $('.site-header');
        
        // Make header sticky on scroll
        $(window).on('scroll', function() {
            if ($(window).scrollTop() > 50) {
                $header.addClass('sticky');
            } else {
                $header.removeClass('sticky');
            }
        });
        
        // Trigger scroll event on page load
        $(window).trigger('scroll');
        
        // Smooth scrolling for anchor links
        $('a[href*="#"]:not([href="#"])').on('click', function() {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && 
                location.hostname === this.hostname) {
                let target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 70 // Adjust for header height
                    }, 1000);
                    return false;
                }
            }
        });
    }
    
    /**
     * Handle tab navigation for ticket types and stadium map
     */
    function setupTabNavigation() {
        const $tabBtns = $('.tab-btn');
        const $tabPanes = $('.tab-pane');
        
        $tabBtns.on('click', function() {
            const tabId = $(this).data('tab');
            
            // Update active tab button
            $tabBtns.removeClass('active');
            $(this).addClass('active');
            
            // Show corresponding tab pane
            $tabPanes.removeClass('active');
            $('#' + tabId + '-tab').addClass('active');
        });
    }
    
    /**
     * Handle quantity control in ticket selection
     */
    function setupQuantityControl() {
        const $minusBtn = $('.quantity-btn.minus');
        const $plusBtn = $('.quantity-btn.plus');
        const $quantityInput = $('.quantity-input');
        const $priceValue = $('.price-value');
        const $totalValue = $('.total-value');
        const $quantityField = $('#quantity-field');
        
        if ($quantityInput.length === 0) {
            return;
        }
        
        const basePrice = parseFloat($priceValue.text().replace(/[^0-9.]/g, ''));
        
        function updateTotal() {
            const quantity = parseInt($quantityInput.val());
            const total = basePrice * quantity;
            
            $totalValue.text('₹' + total.toFixed(2));
            $quantityField.val(quantity);
        }
        
        $minusBtn.on('click', function() {
            let currentValue = parseInt($quantityInput.val());
            
            if (currentValue > 1) {
                $quantityInput.val(currentValue - 1);
                updateTotal();
            }
        });
        
        $plusBtn.on('click', function() {
            let currentValue = parseInt($quantityInput.val());
            const maxValue = parseInt($quantityInput.attr('max') || 10);
            
            if (currentValue < maxValue) {
                $quantityInput.val(currentValue + 1);
                updateTotal();
            }
        });
        
        $quantityInput.on('change', function() {
            let currentValue = parseInt($(this).val());
            const minValue = parseInt($(this).attr('min') || 1);
            const maxValue = parseInt($(this).attr('max') || 10);
            
            // Ensure value is within bounds
            if (currentValue < minValue) {
                $(this).val(minValue);
                currentValue = minValue;
            } else if (currentValue > maxValue) {
                $(this).val(maxValue);
                currentValue = maxValue;
            }
            
            updateTotal();
        });
    }
    
    /**
     * Handle ticket type selection
     */
    function setupTicketTypeSelection() {
        const $ticketTypeOptions = $('.ticket-type-option');
        
        $ticketTypeOptions.on('click', function() {
            $ticketTypeOptions.removeClass('selected');
            $(this).addClass('selected');
            
            // Update seat type in URL and form
            const seatType = $(this).data('type');
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('seat_type', seatType);
            
            // Update the URL without reloading the page
            const newUrl = window.location.pathname + '?' + urlParams.toString();
            window.history.pushState({ path: newUrl }, '', newUrl);
            
            // Update the seat type in the form
            $('input[name="seat_type"]').val(seatType);
        });
    }
    
    /**
     * Initialize payment tabs
     */
    function setupPaymentTabs() {
        const $paymentTabBtns = $('.payment-options-tabs .tab-btn');
        const $paymentPanes = $('.payment-options-tabs .tab-pane');
        
        $paymentTabBtns.on('click', function() {
            const tabId = $(this).data('tab');
            
            // Update active tab button
            $paymentTabBtns.removeClass('active');
            $(this).addClass('active');
            
            // Show corresponding tab pane
            $paymentPanes.removeClass('active');
            $('#' + tabId + '-tab').addClass('active');
        });
    }
    
    /**
     * Copy to clipboard functionality
     */
    function setupCopyToClipboard() {
        $('.copy-btn').on('click', function() {
            const $this = $(this);
            const textToCopy = $this.prev().text();
            
            // Create a temporary textarea to copy from
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(textToCopy).select();
            document.execCommand('copy');
            $temp.remove();
            
            // Show feedback
            const originalText = $this.text();
            $this.text('Copied!');
            
            setTimeout(function() {
                $this.text(originalText);
            }, 2000);
        });
    }
    
    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        initTheme();
        
        // Initialize page-specific functions
        if ($('.selection-tabs').length) {
            setupTabNavigation();
        }
        
        if ($('.quantity-control').length) {
            setupQuantityControl();
        }
        
        if ($('.ticket-type-options').length) {
            setupTicketTypeSelection();
        }
        
        if ($('.payment-options-tabs').length) {
            setupPaymentTabs();
            setupCopyToClipboard();
        }
    });
    
})(jQuery);
