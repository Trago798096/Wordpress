/**
 * Main JavaScript file for IPL Pro theme
 * 
 * @package iplpro
 */

(function() {
    'use strict';

    // Initialize the theme
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
        const siteNavigation = document.getElementById('site-navigation');
        
        if (!menuToggle || !siteNavigation) return;
        
        menuToggle.addEventListener('click', function() {
            siteNavigation.classList.toggle('active');
            menuToggle.setAttribute('aria-expanded', 
                menuToggle.getAttribute('aria-expanded') === 'true' ? 'false' : 'true'
            );
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInside = siteNavigation.contains(event.target) || 
                                  menuToggle.contains(event.target);
            
            if (!isClickInside && siteNavigation.classList.contains('active')) {
                siteNavigation.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /**
     * Setup hero slider functionality
     */
    function setupHeroSlider() {
        const sliderContainer = document.querySelector('.hero-slider');
        
        if (!sliderContainer) return;
        
        const slides = sliderContainer.querySelectorAll('.slider-slide');
        const totalSlides = slides.length;
        let currentSlide = 0;
        let sliderInterval;
        
        // Show the first slide
        showSlide(0);
        
        // Setup auto sliding
        startAutoSlide();
        
        // Add navigation controls if there are multiple slides
        if (totalSlides > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.className = 'slider-nav slider-prev';
            prevBtn.innerHTML = '&larr;';
            prevBtn.setAttribute('aria-label', 'Previous slide');
            
            const nextBtn = document.createElement('button');
            nextBtn.className = 'slider-nav slider-next';
            nextBtn.innerHTML = '&rarr;';
            nextBtn.setAttribute('aria-label', 'Next slide');
            
            sliderContainer.appendChild(prevBtn);
            sliderContainer.appendChild(nextBtn);
            
            prevBtn.addEventListener('click', function() {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                showSlide(currentSlide);
                restartAutoSlide();
            });
            
            nextBtn.addEventListener('click', function() {
                nextSlide();
                restartAutoSlide();
            });
            
            // Pause auto sliding when hovering over slider
            sliderContainer.addEventListener('mouseenter', function() {
                clearInterval(sliderInterval);
            });
            
            sliderContainer.addEventListener('mouseleave', function() {
                startAutoSlide();
            });
            
            // Add swipe support for touch devices
            handleSwipe();
        }
        
        function startAutoSlide() {
            sliderInterval = setInterval(function() {
                nextSlide();
            }, 5000);
        }
        
        function restartAutoSlide() {
            clearInterval(sliderInterval);
            startAutoSlide();
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }
        
        function showSlide(index) {
            slides.forEach(function(slide, i) {
                slide.style.display = i === index ? 'block' : 'none';
                slide.setAttribute('aria-hidden', i === index ? 'false' : 'true');
            });
        }
        
        function handleSwipe() {
            let startX, startY, dist, threshold = 150; // Minimum distance for swipe
            let allowedTime = 300; // Maximum time allowed for swipe
            let elapsedTime, startTime;
            
            sliderContainer.addEventListener('touchstart', function(e) {
                const touchObj = e.changedTouches[0];
                startX = touchObj.pageX;
                startY = touchObj.pageY;
                startTime = new Date().getTime(); // Record time when finger first makes contact
                e.preventDefault();
            }, false);
            
            sliderContainer.addEventListener('touchmove', function(e) {
                e.preventDefault(); // Prevent scrolling when inside slider
            }, false);
            
            sliderContainer.addEventListener('touchend', function(e) {
                const touchObj = e.changedTouches[0];
                dist = touchObj.pageX - startX; // Get horizontal distance traveled
                elapsedTime = new Date().getTime() - startTime; // Get time elapsed
                
                // Check if swipe meets the criteria
                const validSwipe = elapsedTime <= allowedTime && 
                                   Math.abs(dist) >= threshold && 
                                   Math.abs(touchObj.pageY - startY) <= 100;
                
                if (validSwipe) {
                    if (dist > 0) {
                        // Swipe right, go to previous slide
                        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                    } else {
                        // Swipe left, go to next slide
                        currentSlide = (currentSlide + 1) % totalSlides;
                    }
                    showSlide(currentSlide);
                    restartAutoSlide();
                }
                
                e.preventDefault();
            }, false);
        }
    }

    /**
     * Initialize lazy loading images
     */
    function initLazyLoading() {
        if ('loading' in HTMLImageElement.prototype) {
            // Browser supports native lazy loading
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            lazyImages.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
            });
        } else {
            // Fallback for browsers that don't support native lazy loading
            const lazyImages = document.querySelectorAll('.lazy-image');
            
            if (!lazyImages.length) return;
            
            const lazyImageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;
                        lazyImage.classList.remove('lazy-image');
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });
            
            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        }
    }

    /**
     * Setup scroll effects
     */
    function setupScrollEffects() {
        const header = document.querySelector('.site-header');
        let lastScrollTop = 0;
        
        window.addEventListener('scroll', function() {
            const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Add shadow to header when scrolling down
            if (currentScrollTop > 10) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            
            // Show/hide header based on scroll direction
            if (currentScrollTop > lastScrollTop && currentScrollTop > 200) {
                // Scrolling down and not at the top
                header.classList.add('header-hidden');
            } else {
                // Scrolling up
                header.classList.remove('header-hidden');
            }
            
            lastScrollTop = currentScrollTop;
        });
    }

    /**
     * Handle tab navigation for ticket types and stadium map
     */
    function setupTabNavigation() {
        const tabLinks = document.querySelectorAll('.booking-tabs .tab-link');
        
        if (!tabLinks.length) return;
        
        tabLinks.forEach(function(tab) {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                tabLinks.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Show corresponding content
                const tabId = this.getAttribute('data-tab');
                const tabContents = document.querySelectorAll('.tab-content');
                
                tabContents.forEach(function(content) {
                    if (content.getAttribute('id') === tabId) {
                        content.classList.add('active');
                    } else {
                        content.classList.remove('active');
                    }
                });
            });
        });
    }

    /**
     * Handle quantity control in ticket selection
     */
    function setupQuantityControl() {
        const quantityControls = document.querySelectorAll('.quantity-control');
        
        if (!quantityControls.length) return;
        
        quantityControls.forEach(function(control) {
            const decreaseBtn = control.querySelector('.decrease');
            const increaseBtn = control.querySelector('.increase');
            const quantityInput = control.querySelector('input[type="number"]');
            
            if (!decreaseBtn || !increaseBtn || !quantityInput) return;
            
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > parseInt(quantityInput.min || 1)) {
                    quantityInput.value = currentValue - 1;
                    quantityInput.dispatchEvent(new Event('change'));
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                const maxValue = parseInt(quantityInput.max || 10);
                if (currentValue < maxValue) {
                    quantityInput.value = currentValue + 1;
                    quantityInput.dispatchEvent(new Event('change'));
                }
            });
            
            quantityInput.addEventListener('change', function() {
                updateTotal();
            });
        });
        
        function updateTotal() {
            const ticketPrice = document.querySelector('.ticket-price');
            const quantityInput = document.querySelector('.quantity-control input');
            const totalElement = document.querySelector('.booking-total .amount-value');
            
            if (!ticketPrice || !quantityInput || !totalElement) return;
            
            const price = parseFloat(ticketPrice.dataset.price || ticketPrice.textContent.replace(/[^\d.]/g, ''));
            const quantity = parseInt(quantityInput.value);
            
            // Calculate base amount
            const baseAmount = price * quantity;
            
            // Calculate GST (18%)
            const gst = baseAmount * 0.18;
            
            // Service fee (fixed at ₹75)
            const serviceFee = 75;
            
            // Calculate total
            const totalAmount = baseAmount + gst + serviceFee;
            
            // Update displayed values
            const baseAmountElement = document.querySelector('.base-amount .amount-value');
            const gstElement = document.querySelector('.gst-amount .amount-value');
            const serviceFeeElement = document.querySelector('.service-fee .amount-value');
            
            if (baseAmountElement) baseAmountElement.textContent = '₹' + baseAmount.toFixed(2);
            if (gstElement) gstElement.textContent = '₹' + gst.toFixed(2);
            if (serviceFeeElement) serviceFeeElement.textContent = '₹' + serviceFee.toFixed(2);
            
            totalElement.textContent = '₹' + totalAmount.toFixed(2);
        }
    }

    /**
     * Handle ticket type selection
     */
    function setupTicketTypeSelection() {
        const ticketTypeCards = document.querySelectorAll('.ticket-type-card');
        
        if (!ticketTypeCards.length) return;
        
        ticketTypeCards.forEach(function(card) {
            card.addEventListener('click', function() {
                // Remove selected class from all cards
                ticketTypeCards.forEach(c => c.classList.remove('selected'));
                
                // Add selected class to clicked card
                this.classList.add('selected');
                
                // Update selected ticket type in booking summary
                const ticketType = this.querySelector('.ticket-type-name').textContent;
                const ticketPrice = this.querySelector('.ticket-price').textContent;
                const ticketTypeElement = document.querySelector('.selected-ticket-type');
                const ticketPriceElement = document.querySelector('.ticket-price');
                
                if (ticketTypeElement) ticketTypeElement.textContent = ticketType;
                if (ticketPriceElement) {
                    ticketPriceElement.textContent = ticketPrice;
                    ticketPriceElement.dataset.price = ticketPrice.replace(/[^\d.]/g, '');
                }
                
                // Reset quantity to 1
                const quantityInput = document.querySelector('.quantity-control input');
                if (quantityInput) {
                    quantityInput.value = 1;
                    quantityInput.dispatchEvent(new Event('change'));
                }
            });
        });
    }

    /**
     * Initialize payment tabs
     */
    function setupPaymentTabs() {
        const paymentTabs = document.querySelectorAll('.payment-tab');
        
        if (!paymentTabs.length) return;
        
        paymentTabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                paymentTabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Show corresponding content
                const paymentMethod = this.getAttribute('data-payment');
                const paymentContents = document.querySelectorAll('.payment-method-content');
                
                paymentContents.forEach(function(content) {
                    if (content.getAttribute('data-method') === paymentMethod) {
                        content.style.display = 'block';
                    } else {
                        content.style.display = 'none';
                    }
                });
            });
        });
    }

    /**
     * Copy to clipboard functionality
     */
    function setupCopyToClipboard() {
        const copyButtons = document.querySelectorAll('.copy-btn');
        
        if (!copyButtons.length) return;
        
        copyButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-copy');
                
                if (!textToCopy) return;
                
                const tempInput = document.createElement('input');
                tempInput.value = textToCopy;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);
                
                // Show copied message
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                
                setTimeout(() => {
                    this.textContent = originalText;
                }, 2000);
            });
        });
    }
})();