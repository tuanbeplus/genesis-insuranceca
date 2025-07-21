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

    // Suggestion dropdown for insurer search (global and instant)
    jQuery(function($){
        function renderSuggestions(suggestions, $dropdown) {
            // Split suggestions into categories and insurers
            const categories = suggestions.filter(item => item.type === 'category');
            const insurers = suggestions.filter(item => item.type === 'insurer');
            const childCategories = suggestions.filter(item => item.type === 'child_category');

            let html = '';
            // If all are empty, show not found
            if (!categories.length && !insurers.length && !childCategories.length) {
                html = '<div class="no-suggestion">No suggestions found</div>';
                $dropdown.html(html);
                return;
            }

            html += '<div class="suggestion-columns">';
            
            // Only show categories col if not on category page and there are categories or child categories
            const isCategoryPage = $dropdown.closest('form').find('input[name="term_id"]').val();
            if (!isCategoryPage && (categories.length || childCategories.length)) {
                html += '<div class="suggestion-col suggestion-col-cat">';
                html += '<div class="suggestion-col-title">Categories</div>';
                html += '<ul class="suggestion-list">';

                // Group child categories by parent
                const parentGroups = {};
                childCategories.forEach(child => {
                    if (!parentGroups[child.parent_id]) {
                        parentGroups[child.parent_id] = [];
                    }
                    parentGroups[child.parent_id].push(child);
                });

                // Show categories with their children if any
                categories.forEach(function(category){
                    html += '<li class="suggestion-item" data-url="'+category.permalink+'">'
                        + '<span class="suggestion-icon suggestion-category"><i class="fa fa-folder"></i></span>'
                        + '<a href="'+category.permalink+'">'+category.name+'</a>';
                    
                    // If this category has children in search results, show them
                    if (parentGroups[category.id]) {
                        html += '<ul class="child-suggestions">';
                        parentGroups[category.id].forEach(function(child){
                            html += '<li class="suggestion-item child-suggestion" data-url="'+child.permalink+'">'
                                + '<a href="'+child.permalink+'">'+child.name+'</a>'
                                + '</li>';
                        });
                        html += '</ul>';
                    }
                    html += '</li>';
                });

                // Show remaining child categories whose parents weren't in search results
                Object.entries(parentGroups).forEach(([parentId, children]) => {
                    // Skip if we already showed these children under their parent
                    if (!categories.find(cat => cat.id === parseInt(parentId))) {
                        // Use parent info from first child
                        const firstChild = children[0];
                        html += '<li class="suggestion-item parent-category">'
                            + '<span class="suggestion-icon suggestion-category"><i class="fa fa-folder"></i></span>'
                            + '<a href="'+firstChild.parent_link+'">'+firstChild.parent_name+'</a>'
                            + '<ul class="child-suggestions">';
                        
                        children.forEach(function(child){
                            html += '<li class="suggestion-item child-suggestion" data-url="'+child.permalink+'">'
                                + '<a href="'+child.permalink+'">'+child.name+'</a>'
                                + '</li>';
                        });
                        
                        html += '</ul></li>';
                    }
                });

                html += '</ul>';
                html += '</div>';
            }

            // Only show insurers col if there are insurers
            if (insurers.length) {
                html += '<div class="suggestion-col suggestion-col-insurer">';
                html += '<div class="suggestion-col-title">Insurers</div>';
                html += '<ul class="suggestion-list">';
                insurers.forEach(function(item){
                    html += '<li class="suggestion-item" data-url="'+item.permalink+'">'
                        + '<span class="suggestion-icon suggestion-insurer"><i class="fa fa-shield"></i></span>'
                        + '<a href="'+item.permalink+'">'+item.name+'</a>'
                        + '</li>';
                });
                html += '</ul>';
                html += '</div>';
            }
            html += '</div>';
            $dropdown.html(html);
        }

        function searchLocalData(searchTerm, categoriesData, insurersData, categoryFilter = null) {
            let results = [];
            
            if (!searchTerm || searchTerm.length < 2) {
                return results;
            }
            
            const searchLower = searchTerm.toLowerCase();
            
            // Search categories (only if not filtering by category)
            if (!categoryFilter) {
                const matchedCategories = categoriesData.filter(cat => {
                    return cat.name.toLowerCase().includes(searchLower);
                });
                
                // Sort categories: exact matches first, then partial matches
                matchedCategories.sort((a, b) => {
                    const aExact = a.name.toLowerCase() === searchLower ? 1 : 0;
                    const bExact = b.name.toLowerCase() === searchLower ? 1 : 0;
                    if (aExact !== bExact) return bExact - aExact;
                    
                    const aStarts = a.name.toLowerCase().startsWith(searchLower) ? 1 : 0;
                    const bStarts = b.name.toLowerCase().startsWith(searchLower) ? 1 : 0;
                    if (aStarts !== bStarts) return bStarts - aStarts;
                    
                    return a.name.localeCompare(b.name);
                });
                
                results = results.concat(matchedCategories);
            }
            
            // Search insurers (apply category filter if provided)
            const matchedInsurers = insurersData.filter(insurer => {
                if (!insurer.name.toLowerCase().includes(searchLower)) {
                    return false;
                }
                
                // If category filter is active, we'd need to check insurer categories
                // For now, include all matching insurers
                return true;
            });
            
            // Sort insurers: exact matches first, then partial matches
            matchedInsurers.sort((a, b) => {
                const aExact = a.name.toLowerCase() === searchLower ? 1 : 0;
                const bExact = b.name.toLowerCase() === searchLower ? 1 : 0;
                if (aExact !== bExact) return bExact - aExact;
                
                const aStarts = a.name.toLowerCase().startsWith(searchLower) ? 1 : 0;
                const bStarts = b.name.toLowerCase().startsWith(searchLower) ? 1 : 0;
                if (aStarts !== bStarts) return bStarts - aStarts;
                
                return a.name.localeCompare(b.name);
            });
            
            results = results.concat(matchedInsurers);
            
            // Limit results to 10 items
            return results;
        }

        function setupSuggestionDropdown($form) {
            let $input = $form.find('.search-insurer-input');
            let $dropdown = $form.find('.insurer-suggestion-dropdown');
            let termId = $form.find('input[name="term_id"]').val() || '';
            
            // Get JSON data from hidden inputs
            let categoriesData = [];
            let insurersData = [];
            
            try {
                const categoryJson = $form.find('#search_category_json').val();
                const insurerJson = $form.find('#search_insurer_json').val();
                
                if (categoryJson) {
                    categoriesData = JSON.parse(categoryJson);
                }
                if (insurerJson) {
                    insurersData = JSON.parse(insurerJson);
                }
            } catch (e) {
                console.error('Failed to parse JSON data:', e);
            }

            // Store default HTML for reset
            $dropdown.data('default-html', $dropdown.html());

            // Search suggestions on input
            let typingTimer;
            $input.on('input', function(){
                let val = $(this).val();
                
                // Clear any existing timer
                clearTimeout(typingTimer);
                
                if (val.trim() === '') {
                    $dropdown.html($dropdown.data('default-html'));
                    $dropdown.slideDown(120);
                    return;
                }

                // Only search if 2 or more characters
                if (val.trim().length < 2) {
                    $dropdown.slideUp(120);
                    return;
                }

                // Wait for user to finish typing (100ms)
                typingTimer = setTimeout(function() {
                    // Search local data instead of AJAX
                    const suggestions = searchLocalData(val, categoriesData, insurersData, termId);

                    renderSuggestions(suggestions, $dropdown);
                    $dropdown.slideDown(120);
                }, 100);
            });

            // Hide dropdown when clicking outside the input or dropdown
            $(document).on('mousedown.ica-suggestion', function(e){
                if (
                    !$input.is(e.target) && $input.has(e.target).length === 0 &&
                    !$dropdown.is(e.target) && $dropdown.has(e.target).length === 0
                ) {
                    $dropdown.slideUp(120);
                }
            });

            // Click on suggestion
            $dropdown.on('mousedown', '.suggestion-item', function(e){
                e.preventDefault();
                window.location.href = $(this).data('url');
            });
        }

        // Only apply suggestion dropdown to global search form
        setupSuggestionDropdown($('form.insurers-global-search'));
        // Do NOT apply to instant search
    });

} )( window, jQuery )
