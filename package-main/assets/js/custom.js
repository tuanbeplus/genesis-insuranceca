;( function( w, $ ) {
    'use strict';

    function showPopupSharePage(){
        let ctaPopup = $('#insuranceca-share-page > .cta-share');
        let popup = $('#insuranceca-share-page > .content-share-page');
        ctaPopup.click(function(event){
            popup.show( "slow" );
            $('#insuranceca-share-page').addClass('active');
        });
    }

    function hiddenPopupSharePage(){
        let ctaClose = $('#insuranceca-share-page > .content-share-page .cta-close');
        let popup = $('#insuranceca-share-page > .content-share-page');
        ctaClose.click(function(event){
            popup.hide( "slow" );
            $('#insuranceca-share-page').removeClass('active');
        });
    }

    function copyLinkPageCurrent(){

        let ctaCopy = $('#insuranceca-share-page > .content-share-page .cta-copy');

        ctaCopy.click(function(){

            let linkCurrent = document.getElementById("link-page-current");
            linkCurrent.innerHTML = "Copy to clipboard";
            linkCurrent.select();
            linkCurrent.setSelectionRange(0, 99999)
            document.execCommand("copy");

        });

    }

    // funtion hidden alert banner top
    function hiddenAlertBannerTop() {
        let ctaHidden = $('#bt-alert-banner-top .cta-close');
        let isBanner = $('#bt-alert-banner-top')
        ctaHidden.click(function(event){
            isBanner.slideUp(400);
        });

    }

    function btnScroll() {

        $(".elementor-widget-button a").on('click', function(event) {

            if (this.hash !== "") {

                event.preventDefault();

                var hash = this.hash;

                $('html, body').animate({
                    scrollTop: $(hash).offset().top
                }, 800, function(){

                    window.location.hash = hash;
                });
            } // End if
        });

    }

    function toggleMenuMobile() {
    $('.site-header .wp-megamenu .menu-item a').click( function() {
      $(this).find('b').toggleClass('rotate');
    });
  }

    $( document ).ready(function() {
      toggleMenuMobile();
        showPopupSharePage();
        hiddenPopupSharePage();
        copyLinkPageCurrent();
        hiddenAlertBannerTop();
        btnScroll();

    });

    // AJAX logic for insurer instant search and load more
    jQuery(function($){
        let paged = 1;
        function fetchInsurers(reset = false) {
            const formWrapper = $('form.insurers-instant-search');
            let search = formWrapper.find('input.search-insurer-input').val();
            let termId = formWrapper.find('input[name="term_id"]').val();
            let category = formWrapper.find('input[name="insurer_category"]:checked').val() || '';
            let sortBy = $('.insurer-sort-bar input[name="sort_by"]:checked').val() || '';
            if (reset) paged = 1;
            formWrapper.addClass('loading');
            $.ajax({
                url: ICA_AJAX.ajax_url,
                type: 'POST',
                data: {
                    action: 'ica_insurer_search',
                    search: search,
                    category: category,
                    paged: paged,
                    sort_by: sortBy,
                    term_id: termId
                },
                beforeSend: function() {
                    if (reset) $('.insurer-results').html('<div class="loading">Loading...</div>');
                    $('.load-more-insurers').addClass('loading');
                },
                success: function(res) {
                    if (reset) {
                        $('.insurer-results').html(res.html);
                    } else {
                        $('.insurer-results').append(res.html);
                    }
                    if (res.has_more) {
                        $('.load-more-insurers').show();
                    }
                    else {
                        $('.load-more-insurers').hide();
                    }
                    // Update results count
                    if ($('.insurer-results-count').length && typeof res.shown_count !== 'undefined' && typeof res.total_count !== 'undefined') {
                        $('.insurer-results-count').text('Showing ' + res.shown_count + ' of ' + res.total_count + ' results');
                    }
                    // Update URL
                    let params = new URLSearchParams(window.location.search);
                    if (search) {
                        params.set('search', search);
                        $('.key-search').text(search);
                    } else {
                        params.delete('search');
                        $('.key-search').text('');
                    }
                    if (category) {
                        params.set('category', category);
                    } else {
                        params.delete('category');
                    }
                    if (sortBy) {
                        params.set('sort', sortBy);
                    } else {
                        params.delete('sort');
                    }
                    let newUrl = window.location.pathname + '?' + params.toString();
                    window.history.replaceState({}, '', newUrl);
                    formWrapper.removeClass('loading');
                    $('.load-more-insurers').removeClass('loading');
                }
            });
        }
        // On search input with debounce
        let searchTimer;
        $(document).on('input', '.search-insurer-input', function(){
            clearTimeout(searchTimer);
            paged = 1;
            searchTimer = setTimeout(() => {
                fetchInsurers(true);
            }, 500); // 0.5s delay
        });
        // On category change
        $(document).on('change', '.insurers-instant-search input[name="insurer_category"]', function(){
            paged = 1;
            fetchInsurers(true);
            $('#btn-toggle-categories .selected-category').text($(this).data('label'));
            $('#btn-toggle-categories .selected-category').append(
                '<span id="btn-remove-category" role="button" title="Remove Category"><i class="fa fa-close"></i></span>'
            );
            $('#btn-toggle-categories').trigger('click');
        });
        // On form submit
        $(document).on('submit', 'form.insurers-instant-search', function(e){
            e.preventDefault();
            paged = 1;
            fetchInsurers(true);
        });
        // On sort change
        $(document).on('change', 'input[name="sort_by"]', function(){
            paged = 1;
            fetchInsurers(true);
        });
        // Load more
        $(document).on('click', '.load-more-insurers', function(){
            paged++;
            fetchInsurers(false);
        });
    });

    // Toggle category dropdown for insurer instant search
    $(document).on('click', '#btn-toggle-categories', function(e){
        e.preventDefault();
        e.stopPropagation();
        let catDropdown = $('#categories-dropdown');
        if (catDropdown.hasClass('active')) {
            catDropdown.removeClass('active');
        }
        else {
            catDropdown.addClass('active');
            $('#insurer-sort-dropdown').removeClass('active');
            $('#btn-toggle-sort').removeClass('active');
        }
        $(this).toggleClass('active');
    });

    // Toggle insurer sort dropdown
    $(document).on('click', '#btn-toggle-sort', function(e){
        e.preventDefault();
        e.stopPropagation();
        let sortDropdown = $('#insurer-sort-dropdown');
        if (sortDropdown.hasClass('active')) {
            sortDropdown.removeClass('active');
        }
        else {
            sortDropdown.addClass('active');
            $('#categories-dropdown').removeClass('active');
            $('#btn-toggle-categories').removeClass('active');
        }
        $(this).toggleClass('active');
    });

    // Close dropdowns when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#btn-toggle-categories, #categories-dropdown').length) {
            $('#categories-dropdown').removeClass('active');
            $('#btn-toggle-categories').removeClass('active');
        }
        if (!$(e.target).closest('#btn-toggle-sort, #insurer-sort-dropdown').length) {
            $('#insurer-sort-dropdown').removeClass('active');
            $('#btn-toggle-sort').removeClass('active');
        }
    });

    // Remove category filter and reset search
    $(document).on('click', '#btn-remove-category', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        // Reset UI elements
        $('.selected-category').html('Filter by a subcategory');
        $('input[name="insurer_category"]').prop('checked', false);
        
        // Close dropdowns
        $('#categories-dropdown').removeClass('active');
        $('#btn-toggle-categories').removeClass('active');
        
        // Trigger new search without category
        $('form.insurers-instant-search').trigger('submit');
    });

    // Toggle subcategory list under each parent category
    $(document).on('click', '.btn-toggle-options', function(e){
        e.preventDefault();
        e.stopPropagation();
        let $group = $(this).closest('.category-group');
        let $options = $group.find('.category-options-wrapper');
        $options.slideToggle(100);
        $(this).toggleClass('active');
    });

    // Prevent clicks on dropdown from closing it
    $(document).on('click', '#insurer-sort-dropdown, #categories-dropdown, #btn-remove-category, .btn-toggle-category a', function(e){
        e.stopPropagation();
    });

    // Toggle child categories in insurer_categories_list shortcode
    jQuery(document).on('click', '.insurers-categories-list .btn-toggle-category', function(e){
        e.preventDefault();
        var $group = jQuery(this).closest('.category-group');
        var $children = $group.find('.category-children');

        // Accordion: close others
        $group.siblings('.category-group').find('.category-children').slideUp(150);
        $group.siblings('.category-group').find('.btn-toggle-category').removeClass('active');

        // Toggle current
        $children.slideToggle(150);
        jQuery(this).toggleClass('active');
    });

} )( window, jQuery )
