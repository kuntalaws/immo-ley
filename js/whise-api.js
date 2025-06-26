jQuery(document).ready(function($) {
    'use strict';
    
    // Initialize Whise API functionality
    const WhiseAPI = {
        init: function() {
            this.bindEvents();
            this.loadEstates(); // Load initial estates
        },
        
        bindEvents: function() {
            // Filter change events
            $(document).on('change', '.whise-filter', function() {
                WhiseAPI.loadEstates();
            });
            
            // Search button click
            $(document).on('click', '.whise-search-btn', function(e) {
                e.preventDefault();
                WhiseAPI.loadEstates();
            });
            
            // Price range change
            $(document).on('change', '.whise-price-range', function() {
                const selectedRange = $(this).val();
                if (selectedRange) {
                    const [min, max] = selectedRange.split('-');
                    $('.whise-price-min').val(min || '');
                    $('.whise-price-max').val(max || '');
                } else {
                    $('.whise-price-min').val('');
                    $('.whise-price-max').val('');
                }
                WhiseAPI.loadEstates();
            });
        },
        
        loadEstates: function() {
            const filters = this.getFilters();
            
            console.log('Loading estates with filters:', filters);
            
            // Show loading state
            $('#whise-estates-container').addClass('loading');
            
            $.ajax({
                url: whise_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_estates',
                    nonce: whise_ajax.nonce,
                    purpose: filters.purpose,
                    city: filters.city,
                    category: filters.category,
                    price_min: filters.price_min,
                    price_max: filters.price_max
                },
                success: function(response) {
                    console.log('AJAX response:', response);
                    if (response.success && response.data && response.data.length > 0) {
                        console.log('Found', response.data.length, 'estates');
                        WhiseAPI.renderEstates(response.data);
                    } else {
                        console.log('No estates found or error in response');
                        WhiseAPI.showNoResults();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', {xhr: xhr, status: status, error: error});
                    console.error('Failed to load estates');
                    WhiseAPI.showError();
                },
                complete: function() {
                    $('#whise-estates-container').removeClass('loading');
                }
            });
        },
        
        getFilters: function() {
            return {
                purpose: $('.whise-purpose-select').val(),
                city: $('.whise-city-select').val(),
                category: $('.whise-category-select').val(),
                price_min: $('.whise-price-min').val(),
                price_max: $('.whise-price-max').val()
            };
        },
        
        renderEstates: function(estates) {
            const container = $('#whise-estates-container');
            container.empty();
            
            if (estates.length === 0) {
                this.showNoResults();
                return;
            }
            
            estates.forEach(function(estate) {
                const estateHtml = WhiseAPI.createEstateHTML(estate);
                container.append(estateHtml);
            });
        },
        
        createEstateHTML: function(estate) {
            const imageUrl = estate.pictures && estate.pictures.length > 0 
                ? estate.pictures[0].urlLarge 
                : '/wp-content/uploads/2025/06/grid-item-img-01.jpg';
            
            const price = estate.price ? '€ ' + WhiseAPI.formatPrice(estate.price) : 'Prijs op aanvraag';
            const city = estate.city || 'Onbekend';
            const title = estate.name || (estate.shortDescription && estate.shortDescription[0] ? estate.shortDescription[0].content : 'Eigendom');
            
            return '<a href="#" class="filter-grid-item" data-estate-id="' + estate.id + '">' +
                '<div class="filter-grid-item-img">' +
                    '<div class="filter-grid-item-img-box">' +
                        '<img loading="lazy" src="' + imageUrl + '" alt="' + title + '">' +
                    '</div>' +
                '</div>' +
                '<div class="filter-grid-item-info">' +
                    '<div class="filter-grid-item-info-in">' +
                        '<h6><span class="filter-grid-item-info-category">' + city + '</span> / <span class="filter-grid-item-info-price">' + price + '</span></h6>' +
                        '<h4>' + title + '</h4>' +
                    '</div>' +
                '</div>' +
            '</a>';
        },
        
        formatPrice: function(price) {
            if (price >= 1000000) {
                return (price / 1000000).toFixed(1) + 'M';
            } else if (price >= 1000) {
                return (price / 1000).toFixed(0) + 'K';
            } else {
                return price.toLocaleString();
            }
        },
        
        showNoResults: function() {
            const container = $('#whise-estates-container');
            container.html('<div class="no-results">' +
                '<h4>Geen eigendommen gevonden</h4>' +
                '<p>Probeer andere zoekcriteria of neem contact met ons op.</p>' +
            '</div>');
        },
        
        showError: function() {
            const container = $('#whise-estates-container');
            container.html('<div class="error-message">' +
                '<h4>Er is een fout opgetreden</h4>' +
                '<p>Probeer het later opnieuw of neem contact met ons op.</p>' +
            '</div>');
        }
    };
    
    // Initialize if filter elements exist
    if ($('.whise-filter').length > 0) {
        WhiseAPI.init();
    }
}); 