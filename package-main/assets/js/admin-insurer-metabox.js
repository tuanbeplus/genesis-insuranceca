jQuery(document).ready(function($) {
    // Helper to get checked term IDs from the categories checklist
    function getCheckedTermIds() {
        var ids = [];
        $('#insurer-categorychecklist input[type=checkbox]:checked').each(function() {
            ids.push(parseInt($(this).val(), 10));
        });
        return ids;
    }

    // Helper to build the table rows
    function buildDistributionTable(checkedIds, savedMethods) {
        var terms = window.insurerCategoriesTerms || {};
        var order = window.insurerCategoriesOrder || [];
        var checkedSet = {};
        checkedIds.forEach(function(id) { checkedSet[id] = true; });

        // Build a map of parent -> [children]
        var childrenByParent = {};
        order.forEach(function(id) {
            var term = terms[id];
            if (!term) return;
            if (term.parent && checkedSet[id]) {
                if (!childrenByParent[term.parent]) childrenByParent[term.parent] = [];
                childrenByParent[term.parent].push(term);
            }
        });

        var html = '';
        var hasRows = false;
        order.forEach(function(id) {
            var term = terms[id];
            if (!term || term.parent !== 0 || !checkedSet[id]) return;
            var subcats = childrenByParent[term.id] || [];
            // Only include checked subcats
            subcats = subcats.filter(function(subcat) { return checkedSet[subcat.id]; });
            // If no checked subcats, treat main as its own row
            if (subcats.length === 0) subcats = [term];
            var rowspan = subcats.length;
            subcats.forEach(function(subcat, i) {
                hasRows = true;
                var fieldName = 'insurer_distribution[' + subcat.id + ']';
                var val = savedMethods && savedMethods[subcat.id] ? savedMethods[subcat.id] : 'direct';
                html += '<tr>';
                if (i === 0) {
                    html += '<td rowspan="' + rowspan + '" style="vertical-align:middle;background:#fff;font-size:15px;">' + term.name + '</td>';
                }
                html += '<td>' + subcat.name + '</td>';
                html += '<td>';
                ['direct', 'broker', 'both'].forEach(function(key) {
                    var label = {
                        'direct': 'Direct',
                        'broker': 'Through a Broker',
                        'both': 'Both'
                    }[key];
                    html += '<label>';
                    html += '<input type="radio" name="' + fieldName + '" value="' + key + '"' + (val === key ? ' checked' : '') + '> ' + label;
                    html += '</label>';
                });
                html += '</td>';
                html += '</tr>';
            });
        });
        if (!hasRows) {
            html += '<tr><td colspan="3" style="color:#a00;">No checked product types found. Please check product types in the Insurer Categories box.</td></tr>';
        }
        return html;
    }

    // Save current selections so they persist when rebuilding
    function getCurrentSelections() {
        var selections = {};
        $('.insurer-distribution-metabox input[type=radio]:checked').each(function() {
            var name = $(this).attr('name');
            var match = name.match(/insurer_distribution\[(\d+)\]/);
            if (match) {
                selections[match[1]] = $(this).val();
            }
        });
        return selections;
    }

    // Main update function
    function updateDistributionTable() {
        var checkedIds = getCheckedTermIds();
        var savedMethods = getCurrentSelections();
        var html = buildDistributionTable(checkedIds, savedMethods);
        $('.insurer-distribution-metabox tbody').html(html);
    }

    // Listen for changes in the categories checklist
    $('#insurer-categorychecklist').on('change', 'input[type=checkbox]', function() {
        updateDistributionTable();
    });

    // Initial update on page load
    updateDistributionTable();
}); 