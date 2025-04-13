/**
 * Stadium Map JavaScript for IPL Pro theme
 * 
 * @package iplpro
 */

(function() {
    'use strict';

    // Initialize the stadium map functionality on document ready
    document.addEventListener('DOMContentLoaded', function() {
        initStadiumMap();
    });

    /**
     * Initialize the stadium map functionality
     */
    function initStadiumMap() {
        setupSectionClicks();
        setupMapZoom();
        highlightSelectedSection();
    }

    /**
     * Setup section click events
     */
    function setupSectionClicks() {
        const mapSections = document.querySelectorAll('.map-section');
        
        if (!mapSections.length) return;
        
        mapSections.forEach(section => {
            section.addEventListener('click', function() {
                const sectionName = this.getAttribute('data-section');
                const sectionPrice = this.getAttribute('data-price');
                
                // Update section selection in UI
                updateSectionSelection(sectionName);
                
                // Update ticket type based on selected section
                updateTicketType(sectionName, sectionPrice);
                
                // Remove highlight from all sections
                mapSections.forEach(s => s.classList.remove('selected'));
                
                // Add highlight to selected section
                this.classList.add('selected');
            });
        });
    }

    /**
     * Update section selection in UI
     */
    function updateSectionSelection(sectionName) {
        const selectedSectionElement = document.querySelector('.selected-section');
        
        if (selectedSectionElement) {
            selectedSectionElement.textContent = sectionName;
        }
        
        // Show selection message
        const selectionMessage = document.querySelector('.selection-message');
        
        if (selectionMessage) {
            selectionMessage.innerHTML = 'You selected <strong>' + sectionName + '</strong>';
            selectionMessage.style.display = 'block';
        }
    }

    /**
     * Update ticket type based on selected section
     */
    function updateTicketType(sectionName, sectionPrice) {
        const ticketTypeCards = document.querySelectorAll('.ticket-type-card');
        
        if (!ticketTypeCards.length) return;
        
        let matchingTicketFound = false;
        
        // Find the matching ticket type card and select it
        ticketTypeCards.forEach(card => {
            const ticketTypeName = card.querySelector('.ticket-type-name');
            
            if (ticketTypeName && ticketTypeName.textContent.trim() === sectionName) {
                // Trigger click event on matching card
                card.click();
                matchingTicketFound = true;
            }
        });
        
        // If no matching ticket type found, select first one
        if (!matchingTicketFound && ticketTypeCards.length > 0) {
            ticketTypeCards[0].click();
        }
        
        // Update ticket price if needed
        const ticketPriceElement = document.querySelector('.ticket-price');
        
        if (ticketPriceElement && sectionPrice) {
            ticketPriceElement.textContent = '₹' + sectionPrice;
            ticketPriceElement.dataset.price = sectionPrice;
            
            // Trigger quantity change to update totals
            const quantityInput = document.querySelector('.quantity-control input');
            
            if (quantityInput) {
                quantityInput.dispatchEvent(new Event('change'));
            }
        }
    }

    /**
     * Highlight the section related to the currently selected ticket type
     */
    function highlightSelectedSection() {
        const ticketTypeCards = document.querySelectorAll('.ticket-type-card');
        
        if (!ticketTypeCards.length) return;
        
        ticketTypeCards.forEach(card => {
            card.addEventListener('click', function() {
                const ticketTypeName = this.querySelector('.ticket-type-name').textContent.trim();
                const mapSections = document.querySelectorAll('.map-section');
                
                // Remove highlight from all sections
                mapSections.forEach(s => s.classList.remove('selected'));
                
                // Add highlight to matching section
                mapSections.forEach(section => {
                    if (section.getAttribute('data-section') === ticketTypeName) {
                        section.classList.add('selected');
                    }
                });
            });
        });
    }

    /**
     * Enable zooming and panning of stadium map
     */
    function setupMapZoom() {
        const stadiumMap = document.querySelector('.stadium-map');
        
        if (!stadiumMap) return;
        
        const mapSvg = stadiumMap.querySelector('svg');
        
        if (!mapSvg) return;
        
        let scale = 1;
        let panning = false;
        let pointX = 0;
        let pointY = 0;
        let start = { x: 0, y: 0 };
        
        // Add zoom controls
        const zoomInBtn = document.createElement('button');
        zoomInBtn.className = 'zoom-btn zoom-in';
        zoomInBtn.innerHTML = '+';
        zoomInBtn.setAttribute('aria-label', 'Zoom in');
        
        const zoomOutBtn = document.createElement('button');
        zoomOutBtn.className = 'zoom-btn zoom-out';
        zoomOutBtn.innerHTML = '-';
        zoomOutBtn.setAttribute('aria-label', 'Zoom out');
        
        const zoomResetBtn = document.createElement('button');
        zoomResetBtn.className = 'zoom-btn zoom-reset';
        zoomResetBtn.innerHTML = '↻';
        zoomResetBtn.setAttribute('aria-label', 'Reset zoom');
        
        const zoomControls = document.createElement('div');
        zoomControls.className = 'zoom-controls';
        zoomControls.appendChild(zoomInBtn);
        zoomControls.appendChild(zoomOutBtn);
        zoomControls.appendChild(zoomResetBtn);
        
        stadiumMap.appendChild(zoomControls);
        
        // Set initial viewBox
        const initialViewBox = mapSvg.getAttribute('viewBox');
        
        // Add event listeners for zooming
        zoomInBtn.addEventListener('click', function() {
            scale *= 1.2;
            updateMapTransform();
        });
        
        zoomOutBtn.addEventListener('click', function() {
            scale /= 1.2;
            if (scale < 0.5) scale = 0.5; // Limit minimum zoom
            updateMapTransform();
        });
        
        zoomResetBtn.addEventListener('click', function() {
            scale = 1;
            pointX = 0;
            pointY = 0;
            mapSvg.setAttribute('viewBox', initialViewBox);
            mapSvg.style.transform = 'translate(0, 0) scale(1)';
        });
        
        // Add event listeners for panning
        mapSvg.addEventListener('mousedown', function(e) {
            e.preventDefault();
            panning = true;
            start = { x: e.clientX - pointX, y: e.clientY - pointY };
        });
        
        mapSvg.addEventListener('mousemove', function(e) {
            if (!panning) return;
            e.preventDefault();
            pointX = e.clientX - start.x;
            pointY = e.clientY - start.y;
            updateMapTransform();
        });
        
        mapSvg.addEventListener('mouseup', function(e) {
            panning = false;
        });
        
        mapSvg.addEventListener('mouseleave', function(e) {
            panning = false;
        });
        
        // Add event listener for wheel zoom
        mapSvg.addEventListener('wheel', function(e) {
            e.preventDefault();
            const xs = (e.clientX - pointX) / scale;
            const ys = (e.clientY - pointY) / scale;
            
            if (e.deltaY < 0) {
                scale *= 1.1; // Zoom in
            } else {
                scale /= 1.1; // Zoom out
                if (scale < 0.5) scale = 0.5; // Limit minimum zoom
            }
            
            pointX = e.clientX - xs * scale;
            pointY = e.clientY - ys * scale;
            
            updateMapTransform();
        });
        
        // Add touch support for mobile
        mapSvg.addEventListener('touchstart', function(e) {
            if (e.touches.length === 1) {
                e.preventDefault();
                panning = true;
                start = { x: e.touches[0].clientX - pointX, y: e.touches[0].clientY - pointY };
            }
        });
        
        mapSvg.addEventListener('touchmove', function(e) {
            if (!panning || e.touches.length !== 1) return;
            e.preventDefault();
            pointX = e.touches[0].clientX - start.x;
            pointY = e.touches[0].clientY - start.y;
            updateMapTransform();
        });
        
        mapSvg.addEventListener('touchend', function(e) {
            panning = false;
        });
        
        function updateMapTransform() {
            if (scale > 4) scale = 4; // Limit maximum zoom
            mapSvg.style.transform = 'translate(' + pointX + 'px, ' + pointY + 'px) scale(' + scale + ')';
            mapSvg.style.transformOrigin = '0 0';
        }
    }
})();