<?php

// add custom post type
add_action( 'init', 'insuranceca_custom_post_type');
// add custom taxonomy
add_action( 'init', 'insuranceca_custom_taxonomy');

function insuranceca_custom_post_type() {

	// Resources
	register_post_type( 'resources',
		array('labels' => array(
				'name' => __('Resources', 'genesis-insuranceca'), /* This is the Title of the Group */
				'singular_name' => __('Resources', 'genesis-insuranceca'), /* This is the individual type */
				'all_items' => __('All', 'genesis-insuranceca'), /* the all items menu item */
				'add_new' => __('Add New', 'genesis-insuranceca'), /* The add new menu item */
				'add_new_item' => __('Add New', 'genesis-insuranceca'), /* Add New Display Title */
				'edit' => __( 'Edit', 'genesis-insuranceca' ), /* Edit Dialog */
				'edit_item' => __('Edit', 'genesis-insuranceca'), /* Edit Display Title */
				'new_item' => __('New', 'genesis-insuranceca'), /* New Display Title */
				'view_item' => __('View', 'genesis-insuranceca'), /* View Display Title */
				'search_items' => __('Search', 'genesis-insuranceca'), /* Search Custom Type Title */
				'not_found' =>  __('Nothing found in the Database.', 'genesis-insuranceca'), /* This displays if there are no entries yet */
				'not_found_in_trash' => __('Nothing found in Trash', 'genesis-insuranceca'), /* This displays if there is nothing in the trash */
				'parent_item_colon' => ''
			), /* end of arrays */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 10, /* this is what order you want it to appear in on the left hand side menu */
			'menu_icon' => 'dashicons-open-folder', /* the icon for the custom post type menu. uses built-in dashicons (CSS class name) */
			'rewrite'	=> array('slug' => 'resource','with_front' => false), /* you can specify its url slug */
			'has_archive' => false, /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'author', 'thumbnail')
		) /* end of options */
	); /* end of register post type */


	//FAQs
	register_post_type( 'ins-faqs',
		array('labels' => array(
				'name' => __('New CoP Accordion', 'genesis-insuranceca'), /* This is the Title of the Group */
				'singular_name' => __('New CoP Accordion', 'genesis-insuranceca'), /* This is the individual type */
				'all_items' => __('All', 'genesis-insuranceca'), /* the all items menu item */
				'add_new' => __('Add New', 'genesis-insuranceca'), /* The add new menu item */
				'add_new_item' => __('Add New', 'genesis-insuranceca'), /* Add New Display Title */
				'edit' => __( 'Edit', 'genesis-insuranceca' ), /* Edit Dialog */
				'edit_item' => __('Edit', 'genesis-insuranceca'), /* Edit Display Title */
				'new_item' => __('New', 'genesis-insuranceca'), /* New Display Title */
				'view_item' => __('View', 'genesis-insuranceca'), /* View Display Title */
				'search_items' => __('Search', 'genesis-insuranceca'), /* Search Custom Type Title */
				'not_found' =>  __('Nothing found in the Database.', 'genesis-insuranceca'), /* This displays if there are no entries yet */
				'not_found_in_trash' => __('Nothing found in Trash', 'genesis-insuranceca'), /* This displays if there is nothing in the trash */
				'parent_item_colon' => ''
			), /* end of arrays */
			'public' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => false,
			'show_in_admin_status_list' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 10, /* this is what order you want it to appear in on the left hand side menu */
			'menu_icon' => 'dashicons-format-status', /* the icon for the custom post type menu. uses built-in dashicons (CSS class name) */
			'rewrite'	=> array('slug' => 'faq','with_front' => true), /* you can specify its url slug */
			'has_archive' => false, /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
      'supports' => array( 'title', 'editor', 'author', 'thumbnail' , 'page-attributes')
	 	) /* end of options */
	); /* end of register post type */



	//Team
	register_post_type( 'team',
		array('labels' => array(
				'name' => __('Team', 'genesis-insuranceca'), /* This is the Title of the Group */
				'singular_name' => __('Team', 'genesis-insuranceca'), /* This is the individual type */
				'all_items' => __('All', 'genesis-insuranceca'), /* the all items menu item */
				'add_new' => __('Add New', 'genesis-insuranceca'), /* The add new menu item */
				'add_new_item' => __('Add New', 'genesis-insuranceca'), /* Add New Display Title */
				'edit' => __( 'Edit', 'genesis-insuranceca' ), /* Edit Dialog */
				'edit_item' => __('Edit', 'genesis-insuranceca'), /* Edit Display Title */
				'new_item' => __('New', 'genesis-insuranceca'), /* New Display Title */
				'view_item' => __('View', 'genesis-insuranceca'), /* View Display Title */
				'search_items' => __('Search', 'genesis-insuranceca'), /* Search Custom Type Title */
				'not_found' =>  __('Nothing found in the Database.', 'genesis-insuranceca'), /* This displays if there are no entries yet */
				'not_found_in_trash' => __('Nothing found in Trash', 'genesis-insuranceca'), /* This displays if there is nothing in the trash */
				'parent_item_colon' => ''
			), /* end of arrays */
			'public' => true,
			'publicly_queryable' => false,
			'exclude_from_search' => false,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => 10, /* this is what order you want it to appear in on the left hand side menu */
			'menu_icon' => 'dashicons-groups', /* the icon for the custom post type menu. uses built-in dashicons (CSS class name) */
			'rewrite'	=> array('slug' => 'faq','with_front' => false), /* you can specify its url slug */
			'has_archive' => false, /* you can rename the slug here */
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
	  'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'page-attributes')
		) /* end of options */
	); /* end of register post type */

}
function insuranceca_custom_taxonomy(){

	//Types
	register_taxonomy(
        'ins-type',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
        array('resources'), // post type name
        array(
            'hierarchical' => true,
            'label' => 'Types', // display name
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'type',    // This controls the base slug that will display before each term
                'with_front' => false  // Don't display the category base before
            )
        )
    );

		//Topics
		register_taxonomy(
	        'ins-topic',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
	        array('resources'), // post type name
	        array(
	            'hierarchical' => false,
	            'label' => 'Topic', // display name
	            'query_var' => true,
	            'rewrite' => array(
	                'slug' => 'topic',    // This controls the base slug that will display before each term
	                'with_front' => false  // Don't display the category base before
	            )
	        )
	    );

		//Category FAQs
		register_taxonomy(
					'cat-faq',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
					'ins-faqs', // post type name
					array(
							'hierarchical' => true,
							'label' => 'Category', // display name
							'query_var' => true,
							'rewrite' => array(
									'slug' => 'cat-faq',    // This controls the base slug that will display before each term
									'with_front' => false  // Don't display the category base before
							)
					)
			);

}


/**
 * CPT Campaign
 */
function insuranceca_campaign_register() {

	$labels = array(
		'name'               => esc_html__( 'Campaigns', 'influencers' ),
		'singular_name'      => esc_html__( 'Campaign', 'influencers' ),
		'add_new'            => esc_html__( 'Add New', 'influencers' ),
		'add_new_item'       => esc_html__( 'Add New Campaign', 'influencers' ),
		'all_items'          => esc_html__( 'All Campaigns', 'influencers' ),
		'edit_item'          => esc_html__( 'Edit Campaign', 'influencers' ),
		'new_item'           => esc_html__( 'Add New Campaign', 'influencers' ),
		'view_item'          => esc_html__( 'View Item', 'influencers' ),
		'search_items'       => esc_html__( 'Search Campaigns', 'influencers' ),
		'not_found'          => esc_html__( 'No campaign(s) found', 'influencers' ),
		'not_found_in_trash' => esc_html__( 'No campaign(s) found in trash', 'influencers' )
	);

	$args = array(
		'labels'          => $labels,
		'public'          => true,
		'show_ui'         => true,
		'capability_type' => 'page',
		'hierarchical'    => true,
		'menu_icon'       => 'dashicons-admin-post',
		'rewrite'         => array('slug' => 'campaign'), // Permalinks format
		'supports'        => array('title', 'editor', 'thumbnail')
	);

	register_post_type( 'campaign' , $args );
}
add_action('init', 'insuranceca_campaign_register', 1);

/**
 * Register campaign Categories
 */
function insuranceca_campaign_taxonomy() {

	register_taxonomy(
		"campaign_categories",
		array("campaign"),
		array(
			"hierarchical"   => true,
			"label"          => "Categories",
			"singular_label" => "Category",
			"rewrite"        => true
		)
	);

}
add_action('init', 'insuranceca_campaign_taxonomy', 1);

/**
 * Register Custom Post Type for Insurers
 * 
 */
function insuranceca_insurer_register() {
	$labels = array(
		'name'               => esc_html__( 'Insurers', 'genesis-insuranceca' ),
		'singular_name'      => esc_html__( 'Insurer', 'genesis-insuranceca' ),
		'add_new'            => esc_html__( 'Add New', 'genesis-insuranceca' ),
		'add_new_item'       => esc_html__( 'Add New Insurer', 'genesis-insuranceca' ),
		'all_items'          => esc_html__( 'All Insurers', 'genesis-insuranceca' ),
		'edit_item'          => esc_html__( 'Edit Insurer', 'genesis-insuranceca' ),
		'new_item'           => esc_html__( 'Add New Insurer', 'genesis-insuranceca' ),
		'view_item'          => esc_html__( 'View Insurer', 'genesis-insuranceca' ),
		'search_items'       => esc_html__( 'Search Insurers', 'genesis-insuranceca' ),
		'not_found'          => esc_html__( 'No insurers found', 'genesis-insuranceca' ),
		'not_found_in_trash' => esc_html__( 'No insurers found in trash', 'genesis-insuranceca' )
	);
	$args = array(
		'labels'          => $labels,
		'public'          => true,
		'show_ui'         => true,
		'capability_type' => 'post',
		'hierarchical'    => false,
		'menu_icon'       => 'dashicons-shield',
		'supports'        => array('title', 'thumbnail'),
		'has_archive' => true,
	);

	register_post_type( 'insurer' , $args );
}
add_action('init', 'insuranceca_insurer_register', 1);

/**
 * Register insurer Categories
 */
function insuranceca_insurer_taxonomy() {
	register_taxonomy(
		"insurer-category",
		array("insurer"),
		array(
			"hierarchical"   => true,
			"label"          => "Insurer Categories",
			"singular_label" => "Category",
			"rewrite" => true,
		)
	);
}
add_action('init', 'insuranceca_insurer_taxonomy', 999);

if( function_exists('acf_add_options_sub_page') ) {
    acf_add_options_sub_page(array(
        'page_title'    => 'Insurer Settings',
        'menu_title'    => 'Insurer Settings',
        'parent_slug'   => 'edit.php?post_type=insurer',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

// --- Custom Metabox for Insurer Distribution Method ---
add_action('add_meta_boxes', function() {
    add_meta_box(
        'insurer_distribution_method',
        __('Distribution Method by Product Type', 'genesis-insuranceca'),
        'insuranceca_insurer_distribution_metabox',
        'insurer',
        'normal',
        'default'
    );
});

function insuranceca_insurer_distribution_metabox($post) {
    $taxonomy = 'insurer-category';
    $saved = get_post_meta($post->ID, 'insurer_distribution_method', true);

    if (!is_array($saved)) $saved = [];
    // Get checked term IDs from the post
    $checked_terms = wp_get_object_terms($post->ID, $taxonomy, ['fields' => 'ids']);
    if (empty($checked_terms)) {
        echo '<p style="color:#a00;">Please select categories and subcategories in the Insurer Categories box first.</p>';
        return;
    }
    // Get all checked terms (main and subcategories)
    $checked_terms_objs = get_terms([
        'taxonomy' => $taxonomy,
        'include' => $checked_terms,
        'hide_empty' => false,
    ]);
    // Group subcategories by parent
    $main_cats = [];
    $subcats_by_parent = [];
    foreach ($checked_terms_objs as $term) {
        if ($term->parent == 0) {
            $main_cats[$term->term_id] = $term;
        } else {
            $subcats_by_parent[$term->parent][] = $term;
        }
    }
    // Output all terms for JS
    $all_terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ]);
    $terms_js = [];
    foreach ($all_terms as $term) {
        $terms_js[$term->term_id] = [
            'id' => $term->term_id,
            'name' => $term->name,
            'parent' => $term->parent,
        ];
    }
    echo '<script>window.insurerCategoriesTerms = ' . json_encode($terms_js) . ';</script>';
    // Output the order of terms as in the checklist (parents then children, both by name ASC)
    function get_ordered_term_ids($taxonomy) {
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
            'parent' => 0,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);
        $ordered = [];
        foreach ($terms as $parent) {
            $ordered[] = $parent->term_id;
            $children = get_terms([
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
                'parent' => $parent->term_id,
                'orderby' => 'name',
                'order' => 'ASC',
            ]);
            foreach ($children as $child) {
                $ordered[] = $child->term_id;
            }
        }
        return $ordered;
    }
    $ordered_term_ids = get_ordered_term_ids($taxonomy);
    echo '<script>window.insurerCategoriesOrder = ' . json_encode($ordered_term_ids) . ';</script>';
    // Only show main categories that have checked subcategories
    echo '<style>
	.insurer-distribution-metabox {
		margin: 20px 10px 0;
	}
	.insurer-distribution-metabox table.widefat {
		border-collapse: collapse;
		width: 100%;
		background: #fff;
		margin-bottom: 1em;
	}
	.insurer-distribution-metabox table.widefat th, .insurer-distribution-metabox table.widefat td {
		border: 1px solid #e1e1e1;
		padding: 10px 12px;
		vertical-align: middle;
		font-size: 14px;
		color: #333;
	}
	.insurer-distribution-metabox table.widefat th {
		background: #f8f9fa;
		font-weight: 600;
	}
	.insurer-distribution-metabox table.widefat tbody tr:nth-child(even) {
		background: #f6f8fa;
	}
	.insurer-distribution-metabox table tr td label {
		margin-right: 24px;
		font-weight: 400;
		font-size: 13px;
		white-space: nowrap;
		cursor: pointer;
	}
	.insurer-distribution-metabox input[type=radio] {
		margin-right: 4px;
		vertical-align: middle;
	}
	.insurer-distribution-metabox table tr.error {
		background-color: #ffebee !important;
		border-left: 4px solid #f44336;
	}
	.insurer-distribution-metabox table tr.error td {
		color: #d32f2f;
	}
	</style>';
    echo '<div class="insurer-distribution-metabox">';
    echo '<table class="widefat"><thead><tr><th>Category</th><th>Product Type</th><th>Distribution Method</th></tr></thead><tbody>';
    $has_rows = false;
    foreach ($main_cats as $main_id => $main_cat) {
        // If no subcats, treat the main cat as its own subcat
        $subcats = !empty($subcats_by_parent[$main_id]) ? $subcats_by_parent[$main_id] : [ $main_cat ];
        $rowspan = count($subcats);
        foreach ($subcats as $i => $subcat) {
            $has_rows = true;
            $field_name = 'insurer_distribution[' . $subcat->term_id . ']';
            $val = $saved[$subcat->term_id] ?? 'direct';
            echo '<tr>';
            if ($i === 0) {
                echo '<td rowspan="' . $rowspan . '" style="vertical-align:middle;background:#fff;font-size:15px;">' . esc_html($main_cat->name) . '</td>';
            }
            echo '<td>' . esc_html($subcat->name) . '</td>';
            echo '<td>';
            foreach ([
                'direct' => __('Direct', 'genesis-insuranceca'),
                'broker' => __('Through a Broker', 'genesis-insuranceca'),
            ] as $key => $label) {
                echo '<label>';
                echo '<input type="radio" name="' . esc_attr($field_name) . '" value="' . esc_attr($key) . '"' . checked($val, $key, false) . '> ' . esc_html($label);
                echo '</label>';
            }
            echo '</td>';
            echo '</tr>';
        }
    }
    if (!$has_rows) {
        echo '<tr><td colspan="3" style="color:#a00;">No checked product types found. Please check product types in the Insurer Categories box.</td></tr>';
    }
    echo '</tbody></table>';
    echo '</div>';
}

add_action('save_post_insurer', function($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['insurer_distribution'])) {
        $clean = [];
        foreach ($_POST['insurer_distribution'] as $term_id => $method) {
            $term_id = intval($term_id);
            $method = in_array($method, ['direct','broker']) ? $method : 'direct';
            $clean[$term_id] = $method;
        }
        update_post_meta($post_id, 'insurer_distribution_method', $clean);
    } else {
        delete_post_meta($post_id, 'insurer_distribution_method');
    }
});



