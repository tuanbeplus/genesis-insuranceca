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

    // FAI confirmation popup logic
    jQuery(function($){
        const $popup = $('#fai-confirmation-popup');
        if (!$popup.length) return;

        const STORAGE_KEY = 'fai_popup_acknowledged_v1';
        const isAcknowledged = () => {
            try { return localStorage.getItem(STORAGE_KEY) === '1'; } catch(e) { return false; }
        };
        const setAcknowledged = () => {
            try { localStorage.setItem(STORAGE_KEY, '1'); } catch(e) {}
        };

        function openPopup() {
            $popup.attr('aria-hidden', 'false').addClass('active');
            $('body').addClass('fai-popup-open');
        }
        function closePopup() {
            $popup.attr('aria-hidden', 'true').removeClass('active');
            $('body').removeClass('fai-popup-open');
        }

        if (!isAcknowledged()) {
            setTimeout(openPopup, 50);
        }

        $('#fai_btn_continue').on('click', function(){
            const $checks = $('#fai-confirmation-popup .fai-check input[type="checkbox"]');
            let allOk = true;
            $checks.each(function(){
                const ok = $(this).is(':checked');
                $(this).closest('.fai-check').toggleClass('fai-invalid', !ok);
                if (!ok) allOk = false;
            });
            if (allOk) {
                setAcknowledged();
                $('.fai-error').hide();
                $('.fai-check').removeClass('fai-invalid');
                closePopup();
            } else {
                $('.fai-error').show();
            }
        });

        // Clear invalid state on change
        $('#fai-confirmation-popup').on('change', '.fai-check input[type="checkbox"]', function(){
            if ($(this).is(':checked')) {
                $(this).closest('.fai-check').removeClass('fai-invalid');
            }
        });
    });

    // Client-side logic for insurer instant search and load more (no AJAX)
    jQuery(function($){
        let paged = 1;
        const perPage = 10;

        const $formWrapper = $('form.insurers-instant-search');
        const $resultsContainer = $('.insurer-results');
        const $resultsCount = $('.insurer-results-count');
        const $loadMoreBtn = $('.load-more-insurers');

        // Parse JSON datasets from hidden inputs
        let categoriesData = [];
        let insurersData = [];
        try {
            const categoryJson = $formWrapper.find('#search_category_json').val();
            const insurerJson = $formWrapper.find('#search_insurer_json').val();
            categoriesData = categoryJson ? JSON.parse(categoryJson) : [];
            insurersData = insurerJson ? JSON.parse(insurerJson) : [];
        } catch (e) {
            // eslint-disable-next-line no-console
            console.error('Failed to parse insurers/categories JSON', e);
            categoriesData = [];
            insurersData = [];
        }

        // Build category maps/helpers
        const categoriesById = {};
        categoriesData.forEach(c => { categoriesById[parseInt(c.id, 10)] = c; });

        function parentHasChildren(parentId) {
            parentId = parseInt(parentId || 0, 10);
            if (!parentId) return false;
            for (let i = 0; i < categoriesData.length; i++) {
                const c = categoriesData[i];
                if (parseInt(c.parent_id || 0, 10) === parentId) {
                    return true;
                }
            }
            return false;
        }

        function hasAncestor(categoryId, ancestorId) {
            let current = categoriesById[parseInt(categoryId, 10)];
            while (current && current.parent_id && parseInt(current.parent_id, 10) !== 0) {
                if (parseInt(current.parent_id, 10) === parseInt(ancestorId, 10)) return true;
                current = categoriesById[parseInt(current.parent_id, 10)];
            }
            return false;
        }

        function getChildTermIdsOf(parentId) {
            parentId = parseInt(parentId || 0, 10);
            if (!parentId) return [];
            return categoriesData
                .filter(c => parseInt(c.parent_id || 0, 10) === parentId)
                .map(c => parseInt(c.id, 10));
        }

        function getCategoryIdsFromSearch(searchLower) {
            if (!searchLower) return [];
            const matches = categoriesData.filter(c => (c.name || '').toLowerCase().includes(searchLower));
            if (!matches.length) return [];
            // Prefer child category matches; fallback to all matches if none are children
            const childMatches = matches.filter(c => parseInt(c.parent_id || 0, 10) !== 0);
            const chosen = childMatches.length ? childMatches : matches;
            return chosen.map(c => parseInt(c.id, 10));
        }

        function matchesTaxConditions(insurer, pageCatId, selectedCategoryId, categoryIdsFromSearch) {
            const insurerCatIds = (insurer.category_ids || []).map(id => parseInt(id, 10));
            const conditions = [];
            const pageId = parseInt(pageCatId || 0, 10);
            const selectedId = parseInt(selectedCategoryId || 0, 10);

            if (pageId && selectedId) {
                conditions.push(insurerCatIds.includes(selectedId));
            } else if (pageId) {
                // include_children = true
                conditions.push(
                    insurerCatIds.some(cid => cid === pageId || hasAncestor(cid, pageId))
                );
            } else if (selectedId) {
                conditions.push(insurerCatIds.includes(selectedId));
            }

            if (Array.isArray(categoryIdsFromSearch) && categoryIdsFromSearch.length) {
                conditions.push(
                    insurerCatIds.some(cid => categoryIdsFromSearch.includes(cid) || categoryIdsFromSearch.some(sid => hasAncestor(cid, sid)))
                );
            }

            // If no tax conditions built, treat as pass
            if (!conditions.length) return true;
            // Relation OR
            return conditions.some(Boolean);
        }

        function matchesNameSearch(insurer, searchLower, categoryIdsFromSearch) {
            if (!searchLower) return true; // no search
            if (Array.isArray(categoryIdsFromSearch) && categoryIdsFromSearch.length) return true; // mimic PHP: do not apply title search when category search matched
            return (insurer.name || '').toLowerCase().includes(searchLower);
        }

        function anyValueEquals(obj, value) {
            for (const k in obj) { if (Object.prototype.hasOwnProperty.call(obj, k)) { if (obj[k] === value) return true; } }
            return false;
        }

        function matchesDistribution(insurer, method, filterCategoryIds) {
            if (!method || method === 'all') return true;
            const dist = insurer.distribution_map || {};
            const hasMeta = dist && Object.keys(dist).length > 0;
            const catIds = (filterCategoryIds || []).map(id => parseInt(id, 10));

            if (method === 'direct') {
                // If meta not set/empty, treat as direct
                if (!hasMeta) return true;
            }

            if (catIds.length) {
                // Match any selected category
                return catIds.some(cid => {
                    const val = dist[String(cid)] || dist[cid];
                    return method === 'direct' ? (val === 'direct') : (val === 'broker');
                });
            }

            // No filter categories: match any value in the map
            return anyValueEquals(dist, method) || (method === 'direct' && !hasMeta);
        }

        function filterCategoryIdsForDistribution(selectedCategoryId, pageCatId, categoryIdsFromSearch) {
            if (selectedCategoryId) return [parseInt(selectedCategoryId, 10)];
            if (pageCatId) return getChildTermIdsOf(pageCatId);
            if (Array.isArray(categoryIdsFromSearch) && categoryIdsFromSearch.length) return categoryIdsFromSearch;
            return [];
        }

        function sortInsurers(items, sortBy) {
            if (sortBy === 'name_asc') {
                return items.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
            }
            if (sortBy === 'name_desc') {
                return items.sort((a, b) => (b.name || '').localeCompare(a.name || ''));
            }
            // default random
            for (let i = items.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [items[i], items[j]] = [items[j], items[i]];
            }
            return items;
        }

        function buildProductsOfferedText(item, pageCatId, selectedCategoryId) {
            const names = [];
            const ids = (item.category_ids || []).map(id => parseInt(id, 10));
            const pageId = parseInt(pageCatId || 0, 10);
            const selectedId = parseInt(selectedCategoryId || 0, 10);

            if (pageId) {
                const filtered = ids
                    .filter(cid => {
                        const c = categoriesById[cid];
                        return c && parseInt(c.parent_id || 0, 10) === pageId;
                    })
                    .slice(0, 10);
                filtered.forEach(cid => { const c = categoriesById[cid]; if (c) names.push(c.name); });
                // If no child categories exist under this parent, show the parent as a leaf when assigned
                if (!names.length && ids.includes(pageId) && !parentHasChildren(pageId)) {
                    const parentCat = categoriesById[pageId];
                    if (parentCat && parentCat.name) names.push(parentCat.name);
                }
            } else if (selectedId) {
                if (ids.includes(selectedId)) {
                    const c = categoriesById[selectedId];
                    if (c) {
                        if (parseInt(c.parent_id || 0, 10) !== 0) {
                            names.push(c.name);
                        } else if (!parentHasChildren(selectedId)) {
                            // Selected category is a parent without children: treat as leaf
                            names.push(c.name);
                        }
                    }
                }
            } else {
                const filtered = ids
                    .filter(cid => {
                        const c = categoriesById[cid];
                        return c && parseInt(c.parent_id || 0, 10) !== 0;
                    })
                    .slice(0, 10);
                filtered.forEach(cid => { const c = categoriesById[cid]; if (c) names.push(c.name); });
                // If there are no child categories at all, show parent categories that have no children (leaf parents)
                if (!names.length) {
                    const parentLeafs = ids
                        .filter(cid => {
                            const c = categoriesById[cid];
                            return c && parseInt(c.parent_id || 0, 10) === 0 && !parentHasChildren(cid);
                        })
                        .slice(0, 10 - names.length);
                    parentLeafs.forEach(cid => { const c = categoriesById[cid]; if (c) names.push(c.name); });
                }
            }

            if (!names.length) return '';
            return 'Products offered: ' + names.join(', ');
        }

        function buildCardHTML(item, pageCatId, selectedCategoryId) {
            const placeholder = '/wp-content/uploads/2024/08/1c-submissions-bg.png';
            const thumb = item.thumbnail || placeholder;
            let websiteName = item.website_url || '';
            let website = item.website_url || '';
            if (website && !/^https?:\/\//i.test(website)) {
                website = 'https://' + website;
            }
            const phone = item.phone_number || '';
            const products = buildProductsOfferedText(item, pageCatId, selectedCategoryId);
            const productsHtml = products ? '<div class="insurer-products">' + products.replace(/&/g, '&amp;') + '</div>' : '<div class="insurer-products"></div>';
            const websiteHtml = website ? '<div>Website: <a href="' + website + '" target="_blank">' + websiteName + '</a></div>' : '';
            const phoneHtml = phone ? '<div>Phone: <a href="tel:' + phone + '">' + phone + '</a></div>' : '';
            return '' +
                '<div class="insurer-card">' +
                    '<div class="insurer-logo">' +
                        '<img src="' + thumb + '" alt="'+ item.name +' Logo">' +
                    '</div>' +
                    '<div class="insurer-info">' +
                        '<h4><a href="' + item.permalink + '">' + (item.name || '') + '</a></h4>' +
                        '<div class="insurer-meta">' + websiteHtml + phoneHtml + '</div>' +
                        productsHtml +
                        '<a href="' + item.permalink + '" class="view-details">View full details</a>' +
                    '</div>' +
                '</div>';
        }

        let cachedFiltered = [];
        let cachedParamsKey = '';

        function buildParamsKey(search, category, sortBy, termId, distributionMethod) {
            return [search || '', category || '', sortBy || '', termId || '', distributionMethod || ''].join('|');
        }

        function filterAndPrepare(reset) {
            const search = ($formWrapper.find('input.search-insurer-input').val() || '').trim();
            const termId = $formWrapper.find('input[name="term_id"]').val() || '';
            const category = $formWrapper.find('input[name="insurer_category"]:checked').val() || '';
            const sortBy = ($('.insurer-sort-bar input[name="sort_by"]:checked').val() || '');
            const distributionMethod = $formWrapper.find('input[name="distribution_method"]:checked').val() || 'direct';

            // Compute categoryIdsFromSearch and effectiveCategory up-front so they're available
            // even when we return cached results (prevents undefined references)
            const searchLower = search.toLowerCase();
            let categoryIdsFromSearch = getCategoryIdsFromSearch(searchLower);
            if (termId) {
                const pageId = parseInt(termId, 10);
                categoryIdsFromSearch = categoryIdsFromSearch.filter(cid => hasAncestor(cid, pageId) || parseInt((categoriesById[cid] || {}).parent_id || 0, 10) === pageId);
            }
            const effectiveCategory = category || (termId && categoryIdsFromSearch.length ? String(categoryIdsFromSearch[0]) : '');

            const key = buildParamsKey(search, effectiveCategory, sortBy, termId, distributionMethod);
            if (reset) {
                paged = 1;
            }

            if (reset || key !== cachedParamsKey) {
                const filterCategoryIds = filterCategoryIdsForDistribution(effectiveCategory, termId, categoryIdsFromSearch);

                // Filter
                let filtered = insurersData.filter(item => {
                    if (!matchesTaxConditions(item, termId, effectiveCategory, categoryIdsFromSearch)) return false;
                    if (!matchesNameSearch(item, searchLower, categoryIdsFromSearch)) return false;
                    if (!matchesDistribution(item, distributionMethod, filterCategoryIds)) return false;
                    return true;
                });

                // Sort
                filtered = sortInsurers(filtered, sortBy);

                cachedFiltered = filtered;
                cachedParamsKey = key;
            }

            return {
                filtered: cachedFiltered,
                search,
                category: effectiveCategory,
                sortBy,
                termId,
                distributionMethod
            };
        }

        function fetchInsurers(reset = false) {
            $formWrapper.addClass('loading');
            if (reset) $resultsContainer.html('<div class="loading">Loading...</div>');
            $loadMoreBtn.addClass('loading');

            const { filtered, termId, category, search, sortBy, distributionMethod } = filterAndPrepare(reset);

            const total = filtered.length;
            const start = (paged - 1) * perPage;
            const end = Math.min(start + perPage, total);
            const items = filtered.slice(start, end);

            // Render
            const html = items.map(item => buildCardHTML(item, termId, category)).join('');
            if (reset) {
                $resultsContainer.html(html || '<div class="no-results">No insurers found.</div>');
            } else {
                $resultsContainer.append(html);
            }

            const hasMore = end < total;
            if (hasMore) { $loadMoreBtn.show(); } else { $loadMoreBtn.hide(); }

            // Update results count
            if ($resultsCount.length) {
                const shownCount = end;
                $resultsCount.text('Showing ' + shownCount + ' of ' + total + ' results');
            }

            // Update URL (same behavior as before)
            const params = new URLSearchParams(window.location.search);
            if (search) {
                params.set('search', search);
                $('.key-search').text(search);
            } else {
                params.delete('search');
                $('.key-search').text('');
            }
            if (category) { params.set('category', category); } else { params.delete('category'); }
            if (sortBy) { params.set('sort', sortBy); } else { params.delete('sort'); }
            if (distributionMethod && distributionMethod !== 'direct') { params.set('distribution_method', distributionMethod); } else { params.delete('distribution_method'); }
            const queryStr = params.toString();
            const newUrl = window.location.pathname + (queryStr ? ('?' + queryStr) : '');
            window.history.replaceState({}, '', newUrl);

            $formWrapper.removeClass('loading');
            $loadMoreBtn.removeClass('loading');
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
        // On distribution method change
        $(document).on('change', 'input[name="distribution_method"]', function(){
            paged = 1;
            fetchInsurers(true);
        });
        // Load more
        $(document).on('click', '.load-more-insurers', function(){
            paged++;
            fetchInsurers(false);
        });

        // Initial render to replace server output
        fetchInsurers(true);
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
            return results.slice(0, 10);
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
