jQuery(document).ready(function($) {
    // Handle distribution method radio button changes
    $(document).on('change', '.insurer-distribution-metabox input[type="radio"]', function() {
        // Optional: Add any immediate feedback or validation here
        console.log('Distribution method changed:', $(this).val());
    });
    
    // Ensure proper form submission handling
    $(document).on('submit', '#post', function() {
        // Validate that at least one distribution method is selected for each category
        let hasErrors = false;
        $('.insurer-distribution-metabox table tbody tr').each(function() {
            let $row = $(this);
            let $radioGroup = $row.find('input[type="radio"]');
            let checkedCount = $radioGroup.filter(':checked').length;
            
            if (checkedCount === 0) {
                hasErrors = true;
                $row.addClass('error');
            } else {
                $row.removeClass('error');
            }
        });
        
        if (hasErrors) {
            alert('Please select a distribution method for all product types.');
            return false;
        }
        
        return true;
    });
    
    // Add visual feedback for errors
    $(document).on('change', '.insurer-distribution-metabox input[type="radio"]', function() {
        $(this).closest('tr').removeClass('error');
    });
}); 