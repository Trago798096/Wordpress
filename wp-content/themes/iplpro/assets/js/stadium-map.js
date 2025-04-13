/**
 * Stadium Map JavaScript for IPL Pro theme
 * 
 * @package iplpro
 */

(function($) {
    'use strict';
    
    /**
     * Initialize the stadium map functionality
     */
    function initStadiumMap() {
        const $stadiumMap = $('.stadium-map');
        
        if ($stadiumMap.length === 0) {
            return;
        }
        
        // Make sections clickable
        setupSectionClicks();
        
        // Handle initial selection if any
        highlightSelectedSection();
    }
    
    /**
     * Setup section click events
     */
    function setupSectionClicks() {
        // Get sections from SVG
        const $sections = $('.stadium-map .section-path, .stadium-map [data-section]');
        
        $sections.on('click', function() {
            const sectionName = $(this).data('section');
            
            // Remove selected state from all sections
            $sections.removeClass('selected').attr('data-selected', false);
            
            // Add selected state to this section
            $(this).addClass('selected').attr('data-selected', true);
            
            // Update selection in UI
            updateSectionSelection(sectionName);
            
            // Update ticket type based on section
            updateTicketType(sectionName);
        });
        
        // Add hover effects
        $sections.on('mouseenter', function() {
            $(this).addClass('hover');
        }).on('mouseleave', function() {
            $(this).removeClass('hover');
        });
    }
    
    /**
     * Update section selection in UI
     */
    function updateSectionSelection(sectionName) {
        const $sectionInfo = $('.selected-section-info');
        
        if ($sectionInfo.length === 0) {
            // Create section info container if it doesn't exist
            $('.stadium-map-container').append('<div class="selected-section-info"></div>');
        }
        
        $('.selected-section-info').html(`
            <h4>Selected Section: ${sectionName}</h4>
            <p>Click on the map to change your selection</p>
        `);
    }
    
    /**
     * Update ticket type based on selected section
     */
    function updateTicketType(sectionName) {
        const sectionMap = {
            'Premium North': 'Premium Stand',
            'Premium East': 'Premium Stand',
            'Premium South': 'Premium Stand',
            'Premium West': 'Premium Stand',
            'General North-West': 'General Stand',
            'General South-West': 'General Stand',
            'General South-East': 'General Stand',
            'General North-East': 'General Stand',
            'VIP Pavilion': 'VIP Stand',
            'Club House': 'Corporate Box',
            'North Stand': 'General Stand',
            'South Stand': 'General Stand',
            'East Stand': 'General Stand',
            'West Stand': 'General Stand',
        };
        
        // Get corresponding ticket type
        const ticketType = sectionMap[sectionName] || 'General Stand';
        
        // Find and select the ticket type in the UI
        const $ticketTypes = $('.ticket-type-option');
        $ticketTypes.removeClass('selected');
        $ticketTypes.filter(`[data-type="${ticketType}"]`).addClass('selected');
        
        // Update the hidden field
        $('input[name="seat_type"]').val(ticketType);
        
        // Update URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('seat_type', ticketType);
        
        // Update URL without page reload
        const newUrl = window.location.pathname + '?' + urlParams.toString();
        window.history.pushState({ path: newUrl }, '', newUrl);
    }
    
    /**
     * Highlight the section related to the currently selected ticket type
     */
    function highlightSelectedSection() {
        const urlParams = new URLSearchParams(window.location.search);
        const seatType = urlParams.get('seat_type');
        
        if (!seatType) {
            return;
        }
        
        // Map from ticket type to section
        const typeToSection = {
            'General Stand': 'General North-West',
            'Premium Stand': 'Premium North',
            'VIP Stand': 'VIP Pavilion',
            'Corporate Box': 'Club House',
            'Pavilion Stand': 'Premium East',
        };
        
        const sectionName = typeToSection[seatType] || 'General North-West';
        
        // Find section with the given name
        const $section = $(`.stadium-map [data-section="${sectionName}"]`);
        
        if ($section.length > 0) {
            $section.addClass('selected').attr('data-selected', true);
            updateSectionSelection(sectionName);
        }
    }
    
    /**
     * Enable zooming and panning of stadium map
     */
    function setupMapZoom() {
        const $mapContainer = $('.stadium-map-container');
        const $stadiumMap = $('.stadium-map');
        
        if ($mapContainer.length === 0 || $stadiumMap.length === 0) {
            return;
        }
        
        // Add zoom controls
        $mapContainer.append(`
            <div class="map-zoom-controls">
                <button type="button" class="zoom-in" title="Zoom in">+</button>
                <button type="button" class="zoom-out" title="Zoom out">-</button>
                <button type="button" class="zoom-reset" title="Reset zoom">Reset</button>
            </div>
        `);
        
        let currentScale = 1;
        let isDragging = false;
        let startX, startY, translateX = 0, translateY = 0;
        
        // Handle zoom in
        $('.zoom-in').on('click', function() {
            currentScale += 0.2;
            if (currentScale > 3) {
                currentScale = 3; // Maximum zoom level
            }
            updateMapTransform();
        });
        
        // Handle zoom out
        $('.zoom-out').on('click', function() {
            currentScale -= 0.2;
            if (currentScale < 0.5) {
                currentScale = 0.5; // Minimum zoom level
            }
            updateMapTransform();
        });
        
        // Handle zoom reset
        $('.zoom-reset').on('click', function() {
            currentScale = 1;
            translateX = 0;
            translateY = 0;
            updateMapTransform();
        });
        
        // Handle mouse wheel zoom
        $mapContainer.on('wheel', function(e) {
            e.preventDefault();
            
            if (e.originalEvent.deltaY < 0) {
                // Zoom in
                currentScale += 0.1;
                if (currentScale > 3) {
                    currentScale = 3;
                }
            } else {
                // Zoom out
                currentScale -= 0.1;
                if (currentScale < 0.5) {
                    currentScale = 0.5;
                }
            }
            
            updateMapTransform();
        });
        
        // Handle dragging
        $stadiumMap.on('mousedown', function(e) {
            isDragging = true;
            startX = e.clientX - translateX;
            startY = e.clientY - translateY;
            $stadiumMap.css('cursor', 'grabbing');
        });
        
        $(document).on('mousemove', function(e) {
            if (isDragging) {
                translateX = e.clientX - startX;
                translateY = e.clientY - startY;
                updateMapTransform();
            }
        });
        
        $(document).on('mouseup', function() {
            isDragging = false;
            $stadiumMap.css('cursor', 'grab');
        });
        
        // Touch events for mobile
        $stadiumMap.on('touchstart', function(e) {
            const touch = e.originalEvent.touches[0];
            isDragging = true;
            startX = touch.clientX - translateX;
            startY = touch.clientY - translateY;
        });
        
        $(document).on('touchmove', function(e) {
            if (isDragging) {
                const touch = e.originalEvent.touches[0];
                translateX = touch.clientX - startX;
                translateY = touch.clientY - startY;
                updateMapTransform();
            }
        });
        
        $(document).on('touchend', function() {
            isDragging = false;
        });
        
        // Update transform
        function updateMapTransform() {
            $stadiumMap.css('transform', `translate(${translateX}px, ${translateY}px) scale(${currentScale})`);
        }
        
        // Initial setup
        $stadiumMap.css('cursor', 'grab');
    }
    
    /**
     * Initialize on document ready
     */
    $(document).ready(function() {
        initStadiumMap();
        setupMapZoom();
    });
    
})(jQuery);
