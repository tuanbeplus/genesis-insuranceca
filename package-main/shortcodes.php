<?php


/**
 * Render global search form for insurers
 * 
 * @return string HTML output of the search form
 */
add_shortcode( 'insurers_global_search', 'ica_render_insurers_global_search' );
function ica_render_insurers_global_search($atts) {
    $atts = shortcode_atts( array(
        'categories' => 'false',
    ), $atts, 'insurers_global_search' );

	$search_form = get_field('search_insurer_form', 'option');
	$heading = !empty($search_form['form_heading']) ? $search_form['form_heading'] : 'Find an insurer search';
	$placeholder = !empty($search_form['input_placeholder']) ? $search_form['input_placeholder'] : 'Enter search term';
	$suggestions = $search_form['suggestions'] ?? [];

	// Get all categories for JSON
	$all_categories = get_terms([
		'taxonomy' => 'insurer-category',
		'hide_empty' => false,
		'number' => 0, // Get all
	]);
	$categories_json = [];
	if (!empty($all_categories) && !is_wp_error($all_categories)) {
		foreach ($all_categories as $cat) {
			$categories_json[] = [
				'type' => $cat->parent == 0 ? 'category' : 'child_category',
				'id' => $cat->term_id,
				'name' => $cat->name,
				'permalink' => $cat->parent == 0 ? get_term_link($cat) : get_term_link(get_term($cat->parent)) . '?category=' . $cat->term_id,
				'parent_id' => $cat->parent,
				'parent_name' => $cat->parent ? get_term($cat->parent)->name : '',
				'parent_link' => $cat->parent ? get_term_link(get_term($cat->parent)) : ''
			];
		}
	}

	// Get all insurers for JSON
	$all_insurers = get_posts([
		'post_type' => 'insurer',
		'posts_per_page' => -1,
		'post_status' => 'publish'
	]);
	$insurers_json = [];
	foreach ($all_insurers as $insurer) {
		$insurers_json[] = [
			'type' => 'insurer',
			'id' => $insurer->ID,
			'name' => $insurer->post_title,
			'permalink' => get_permalink($insurer->ID)
		];
	}

	ob_start();
?>
	<form action="/insurer/" method="GET" class="insurers-global-search search-form">
        <input type="hidden" id="search_category_json" value="<?php echo esc_attr(json_encode($categories_json)) ?>">
        <input type="hidden" id="search_insurer_json" value="<?php echo esc_attr(json_encode($insurers_json)) ?>">
		<h3><?php echo $heading ?></h3>
		<div class="input-container">
			<input class="search-insurer-input" 
				type="text" name="search" value="" 
				placeholder="<?php echo $placeholder ?>" 
				autocomplete="off" spellcheck="false" dir="auto">
			<button class="btn-search-insurer" type="submit" title="Search"><i class="fa fa-search"></i></button>
			<?php
			// Fetch parent categories for initial dropdown
			$parent_cats = get_terms([
				'taxonomy' => 'insurer-category',
				'hide_empty' => false,
				'parent' => 0,
				'number' => 12,
			]);
			?>
			<ul class="insurer-suggestion-dropdown" style="display:none;">
				<?php if (!empty($parent_cats) && !is_wp_error($parent_cats)): ?>
					<?php foreach ($parent_cats as $cat): ?>
						<li class="suggestion-item" data-url="<?php echo esc_url(get_term_link($cat)); ?>">
							<span class="suggestion-icon suggestion-category"><i class="fa fa-folder"></i></span>
							<a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
						</li>
					<?php endforeach; ?>
				<?php else: ?>
					<li class="no-suggestion">No suggestions found</li>
				<?php endif; ?>
			</ul>
		</div>
		<?php if (!empty($suggestions)): ?>
			<div class="suggestions-container">
				<span>Suggestions:</span>
				<ul class="suggestions-list">
				<?php 
				$count = count($suggestions);
				$i = 0;
				foreach ($suggestions as $item): 
					if (!empty($item['suggestion'])):
						$i++;
					?>
					<li class="suggestion">
						<a href="/insurer/?search=<?php echo esc_attr($item['suggestion']) ?>"><?php echo esc_html($item['suggestion']); ?></a><?php if ($i < $count) echo ','; ?>
					</li>
					<?php 
					endif;
				endforeach; 
				?>
				</ul>
			</div>
		<?php endif; ?>
	</form>

	<?php if ($atts['categories'] === 'true'): ?>
	<div class="insurer-categories-wrapper">
	<?php
	$terms = get_terms( array(
		'taxonomy' => 'insurer-category',
		'hide_empty' => false,
		'parent' => 0
	) );

	if (!empty($terms) && !is_wp_error($terms)) {
		echo '<h3>Or browse by category</h3>';
		echo '<ul class="insurer-categories-buttons">';
		foreach ($terms as $term) {
			echo '<li><a href="' . get_term_link($term) . '">' . esc_html($term->name) . '</a></li>';
		}
		echo '</ul>';
	}
	?>
	</div>
	<?php endif; ?>
<?php
    return ob_get_clean();
}

/**
 * Retrieves and renders insurer query results.
 *
 * This shared function handles querying the 'insurer' post type based on search, category, and sorting parameters,
 * and returns the HTML and meta information for displaying insurer results.
 */
function ica_get_insurer_query_results($params) {
    $paged = isset($params['paged']) ? intval($params['paged']) : 1;
    $per_page = isset($params['per_page']) ? intval($params['per_page']) : 10;
    $search = isset($params['search']) ? sanitize_text_field($params['search']) : '';
    $category = isset($params['category']) ? intval($params['category']) : 0;
    $sort_by = isset($params['sort_by']) ? sanitize_text_field($params['sort_by']) : '';
    $force_category = isset($params['force_category']) ? intval($params['force_category']) : 0;
    $distribution_method = isset($params['distribution_method']) ? sanitize_text_field($params['distribution_method']) : '';

    // Search by category name
    $category_ids_from_search = [];
    if (!empty($search)) {
        $cat_terms = get_terms([
            'taxonomy' => 'insurer-category',
            'hide_empty' => false,
            'name__like' => $search,
            'fields' => 'ids',
        ]);
        if (!is_wp_error($cat_terms) && !empty($cat_terms)) {
            $category_ids_from_search = $cat_terms;
        }
    }

    // Tax query
    $tax_query = [];
    if ($force_category && $category) {
        $tax_query[] = [
            'taxonomy' => 'insurer-category',
            'field' => 'term_id',
            'terms' => [$category],
            'include_children' => false,
        ];
    } elseif ($force_category) {
        $tax_query[] = [
            'taxonomy' => 'insurer-category',
            'field' => 'term_id',
            'terms' => [$force_category],
            'include_children' => true,
        ];
    } elseif ($category) {
        $tax_query[] = [
            'taxonomy' => 'insurer-category',
            'field' => 'term_id',
            'terms' => [$category],
            'include_children' => false,
        ];
    }

    if (!empty($category_ids_from_search)) {
        $tax_query[] = [
            'taxonomy' => 'insurer-category',
            'field' => 'term_id',
            'terms' => $category_ids_from_search,
            'include_children' => true,
        ];
    }

    // Meta query
    $meta_query = [];

    if (!empty($distribution_method) && $distribution_method !== 'all') {
        $filter_category_ids = [];

        if (!empty($category)) {
            $filter_category_ids[] = $category;
        } elseif (!empty($force_category)) {
            $child_terms = get_terms([
                'taxonomy' => 'insurer-category',
                'hide_empty' => false,
                'parent' => $force_category,
                'fields' => 'ids',
            ]);
            if (!is_wp_error($child_terms)) {
                $filter_category_ids = $child_terms;
            }
        } elseif (!empty($category_ids_from_search)) {
            $filter_category_ids = $category_ids_from_search;
        }

        $filter_category_ids = array_unique(array_filter($filter_category_ids));
        $or_conditions = ['relation' => 'OR'];

        if (!empty($filter_category_ids)) {
            foreach ($filter_category_ids as $cat_id) {
                $or_conditions[] = [
                    'key' => 'insurer_distribution_method',
                    'value' => sprintf('i:%d;s:%d:"%s";', $cat_id, strlen($distribution_method), $distribution_method),
                    'compare' => 'LIKE',
                ];
            }
        } else {
            $or_conditions[] = [
                'key' => 'insurer_distribution_method',
                'value' => sprintf('s:%d:"%s"', strlen($distribution_method), $distribution_method),
                'compare' => 'LIKE',
            ];
        }

        if ($distribution_method === 'direct') {
            $or_conditions[] = [
                'key' => 'insurer_distribution_method',
                'compare' => 'NOT EXISTS'
            ];
            $or_conditions[] = [
                'key' => 'insurer_distribution_method',
                'value' => '',
                'compare' => '='
            ];
        }

        $meta_query[] = $or_conditions;
    }

    // Build query args
    $args = [
        'post_type' => 'insurer',
        'posts_per_page' => $per_page,
        'paged' => $paged,
    ];

    if (empty($category_ids_from_search)) {
        $args['s'] = $search;
    }

    if (!empty($tax_query)) {
        $args['tax_query'] = ['relation' => 'OR'] + $tax_query;
    }

    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }

    // Sorting
    if ($sort_by === 'name_asc') {
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
    } elseif ($sort_by === 'name_desc') {
        $args['orderby'] = 'title';
        $args['order'] = 'DESC';
    } else {
        $args['orderby'] = 'rand';
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $through_a_broker = get_field('through_a_broker', get_the_ID());
            $broker_url = get_field('broker_website_url', 'option') ?: 'https://www.needabroker.com.au/';
            $distribution_map = get_post_meta(get_the_ID(), 'insurer_distribution_method', true);
            $distribution_map = is_array($distribution_map) ? $distribution_map : [];
            ?>
            <div class="insurer-card">
                <div class="insurer-logo">
                    <?php if (has_post_thumbnail()) : the_post_thumbnail('medium');
                    else : ?>
                        <img src="<?php echo esc_url('/wp-content/uploads/2024/08/1c-submissions-bg.png') ?>"
                             style="object-fit:cover;" alt="ICA Placeholder">
                    <?php endif; ?>
                </div>
                <div class="insurer-info">
                    <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    <div class="insurer-meta">
                        <?php if ($through_a_broker): ?>
                            <div>Products offered <a href="<?php echo esc_url($broker_url); ?>" target="_blank">through a broker only</a></div>
                        <?php endif; ?>
                        <?php if ($websites = get_field('websites')): ?>
                            <div>Website: <a href="<?php echo esc_url($websites[0]['website_url']); ?>" target="_blank"><?php echo esc_html($websites[0]['website_url']); ?></a></div>
                        <?php endif; ?>
                    </div>
                    <div class="insurer-products">
                        <?php
                        if (!empty($force_category)) {
                            $post_categories = get_the_terms(get_the_ID(), 'insurer-category');
                            if ($post_categories && !is_wp_error($post_categories)) {
                                $child_categories = array_filter($post_categories, fn($cat) => $cat->parent == $force_category);
                                $filtered = [];
                                foreach ($child_categories as $cat) {
                                    $cat_id = $cat->term_id;
                                    $method = $distribution_map[$cat_id] ?? 'direct';
                                    $match = $distribution_method === 'all' || 
                                            ($distribution_method === 'direct' && in_array($method, ['direct', ''])) ||
                                            ($distribution_method === 'broker' && $method === 'broker') ||
                                            ($distribution_method === 'both' && $method === 'both');
                                    if ($match) {
                                        $filtered[] = $cat;
                                    }
                                    if (count($filtered) >= 10) break;
                                }
                                if (!empty($filtered)) {
                                    $category_names = array_map(fn($cat) => $cat->name, $filtered);
                                    echo 'Products offered: ' . esc_html(implode(', ', $category_names));
                                }
                            }
                        } elseif (!empty($category)) {
                            if (has_term($category, 'insurer-category', get_the_ID())) {
                                $method = $distribution_map[$category] ?? 'direct';
                                $match = $distribution_method === 'all' || 
                                        ($distribution_method === 'direct' && in_array($method, ['direct', ''])) ||
                                        ($distribution_method === 'broker' && $method === 'broker') ||
                                        ($distribution_method === 'both' && $method === 'both');
                                if ($match) {
                                    $term = get_term($category, 'insurer-category');
                                    if ($term && !is_wp_error($term) && $term->parent != 0) {
                                        echo 'Products offered: ' . esc_html($term->name);
                                    }
                                }
                            }
                        } else {
                            $post_categories = get_the_terms(get_the_ID(), 'insurer-category');
                            if ($post_categories && !is_wp_error($post_categories)) {
                                $child_categories = array_filter($post_categories, fn($cat) => $cat->parent != 0);
                                $filtered = [];
                                foreach ($child_categories as $cat) {
                                    $cat_id = $cat->term_id;
                                    $method = $distribution_map[$cat_id] ?? 'direct';
                                    $match = $distribution_method === 'all' || 
                                            ($distribution_method === 'direct' && in_array($method, ['direct', ''])) ||
                                            ($distribution_method === 'broker' && $method === 'broker') ||
                                            ($distribution_method === 'both' && $method === 'both');
                                    if ($match) {
                                        $filtered[] = $cat;
                                    }
                                    if (count($filtered) >= 10) break;
                                }
                                if (!empty($filtered)) {
                                    $category_names = array_map(fn($cat) => $cat->name, $filtered);
                                    echo 'Products offered: ' . esc_html(implode(', ', $category_names));
                                }
                            }
                        }
                        ?>
                    </div>
                    <a href="<?php the_permalink(); ?>" class="view-details">View full details</a>
                </div>
            </div>
            <?php
        }
    } else {
        echo '<div class="no-results">No insurers found.</div>';
    }

    wp_reset_postdata();

    $html = ob_get_clean();
    $shown_count = $query->post_count + ($paged - 1) * $per_page;
    $total_count = $query->found_posts;
    $has_more = ($query->max_num_pages > $paged);

    return [
        'html' => $html,
        'shown_count' => $shown_count,
        'total_count' => $total_count,
        'has_more' => $has_more,
    ];
}

/**
 * Renders a category group with toggleable options for insurer categories
 */
function ica_render_category_group($term_obj) {
	echo '<div class="category-group">';
    echo '<button class="btn-toggle-options">
        <span>'. esc_html($term_obj->name) .'</span>
        <span class="icon"><i class="fa fa-angle-down"></i></span>
    </button>';
	$child_terms = get_terms(array(
		'taxonomy' => 'insurer-category',
		'hide_empty' => true,
		'parent' => $term_obj->term_id
	));
	
	if (!empty($child_terms) && !is_wp_error($child_terms)) {
		echo '<div class="category-options-wrapper">';
		echo '<ul class="category-options">';
		foreach ($child_terms as $child_term) {
			$checked = (isset($_GET['category']) && $_GET['category'] == $child_term->term_id) ? 'checked' : '';
			echo '<li>';
			echo '<input type="radio" name="insurer_category" data-label="'.esc_html($child_term->name).'" id="category-' . $child_term->term_id . '" value="' . $child_term->term_id . '" ' . $checked . '>';
			echo '<label for="category-' . $child_term->term_id . '">' . esc_html($child_term->name) . '</label>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	} else {
		// If no child terms, show the parent term itself
		echo '<div class="category-options-wrapper">';
		echo '<ul class="category-options">';
		$checked = (isset($_GET['category']) && $_GET['category'] == $term_obj->term_id) ? 'checked' : '';
		echo '<li>';
		echo '<input type="radio" name="insurer_category" data-label="'.esc_html($term_obj->name).'" id="category-' . $term_obj->term_id . '" value="' . $term_obj->term_id . '" ' . $checked . '>';
		echo '<label for="category-' . $term_obj->term_id . '">' . esc_html($term_obj->name) . '</label>';
		echo '</li>';
		echo '</ul>';
		echo '</div>';
	}
	echo '</div>';
}

add_shortcode( 'insurers_instant_search', 'ica_render_insurers_instant_search' );
function ica_render_insurers_instant_search($atts) {
    $atts = shortcode_atts( array(
        'category' => '',
    ), $atts, 'insurers_instant_search' );

    $search_form = get_field('search_insurer_form', 'option');
    $heading = !empty($search_form['form_heading']) ? $search_form['form_heading'] : 'Find an insurer search';
    $placeholder = !empty($search_form['input_placeholder']) ? $search_form['input_placeholder'] : 'Enter search term';
    if (!empty($atts['category'])) {
        $term = get_term($atts['category']);
        if ($term && !is_wp_error($term)) {
            $placeholder = 'Search ' . esc_html($term->name);
        }
    }
    $landing = get_field('find_an_insurer_landing', 'option');
    $landing = !empty($landing) ? $landing : '/find-an-insurer/';

    // Get values from URL
    $search_val = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $category_val = isset($_GET['category']) ? intval($_GET['category']) : 0;
    $sort_val = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : '';

    ob_start();
?>
    <form onsubmit="return false" method="POST" class="insurers-instant-search search-form">
        <input type="hidden" name="term_id" value="<?php echo $atts['category'] ?? '' ?>">
        <h3><?php echo $heading ?></h3>
        <div class="input-container">
            <input class="search-insurer-input" 
                type="text" name="search" 
                value="<?php echo esc_attr($search_val) ?>" 
                placeholder="<?php echo $placeholder ?>" 
                autocomplete="off" spellcheck="false" dir="auto">
            <button class="btn-search-insurer" type="submit" title="Search"><i class="fa fa-search"></i></button>
            <!-- Suggestion dropdown removed for instant search -->
        </div>
        <div class="categories-container">
            <button id="btn-toggle-categories">
                <div class="selected-category">
					<?php if ($category_val): 
						$term = get_term($category_val);
						if ($term && !is_wp_error($term)) {
							echo esc_html($term->name);
						}
						?>
						<span id="btn-remove-category" role="button" title="Remove Category"><i class="fa fa-close"></i></span>
					<?php else: echo 'Filter by product type' ?>
					<?php endif; ?>
				</div>
                <span class="icon"><i class="fa fa-angle-down"></i></span>
            </button>
            <div id="categories-dropdown">
                <p>Select a product type</p>
            <?php
                $parent_terms = get_terms(array(
                    'taxonomy' => 'insurer-category',
                    'hide_empty' => true,
                    'parent' => 0
                ));
                if (!empty($parent_terms) && !is_wp_error($parent_terms)) {
                    foreach ($parent_terms as $parent_term) {
						if (!empty($atts['category'])) {
							if ($atts['category'] == $parent_term->term_id) {
								ica_render_category_group($parent_term);
							}
						}
						else {
							ica_render_category_group($parent_term);
						}
                    }
                }
            ?>
            </div>
        </div>
        <div class="distribution-method-container">
            <h4>Distribution Method:</h4>
            <ul class="distribution-method-options">
                <?php
                $distribution_methods = [
                    'direct' => 'Direct',
                    'broker' => 'Through a Broker',
                    'both' => 'Both',
                    'all' => 'All'
                ];
                $selected_method = isset($_GET['distribution_method']) ? sanitize_text_field($_GET['distribution_method']) : 'direct';
                foreach ($distribution_methods as $value => $label):
                    $input_id = 'distribution_method_' . $value;
                ?>
                    <li>
                        <input 
                            id="<?php echo esc_attr($input_id); ?>" 
                            type="radio" 
                            name="distribution_method" 
                            value="<?php echo esc_attr($value); ?>" 
                            <?php checked($selected_method, $value); ?>
                        >
                        <label for="<?php echo esc_attr($input_id); ?>"><?php echo esc_html($label); ?></label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <a id="btn-back" href="<?php echo esc_url($landing) ?>">
            <span class="icon"><i class="fa fa-angle-left"></i></span>
            <span>Back to Find an insurer search</span>
        </a>
    </form>
    <div class="insurer-sort-bar">
        <p class="insurer-results-count"></p>
        <button id="btn-toggle-sort">
            <span>Sort by</span>
            <span class="icon"><i class="fa fa-angle-down"></i></span>
            <div id="insurer-sort-dropdown">
                <input type="radio" name="sort_by" id="sort-name-default" value="" <?php echo ($sort_val === '') ? 'checked' : '' ?>>
                <label for="sort-name-default">
                    <span>Default</span>
                </label>
                <input type="radio" name="sort_by" id="sort-name-asc" value="name_asc" <?php echo ($sort_val === 'name_asc') ? 'checked' : '' ?>>
                <label for="sort-name-asc">
                    <span>Name Ascending</span>
                </label>
                <input type="radio" name="sort_by" id="sort-name-desc" value="name_desc" <?php echo ($sort_val === 'name_desc') ? 'checked' : '' ?>>
                <label for="sort-name-desc">
                    <span>Name Descending</span>
                </label>
            </div>
        </button>
    </div>
    <?php
    // Use shared function for initial results
    $params = [
        'search' => $search_val,
        'category' => $category_val,
        'sort_by' => $sort_val,
        'paged' => 1,
        'per_page' => 10,
        'force_category' => !empty($atts['category']) ? intval($atts['category']) : 0,
        'distribution_method' => isset($_GET['distribution_method']) ? sanitize_text_field($_GET['distribution_method']) : 'direct',
    ];
    $results = ica_get_insurer_query_results($params);
    ?>
    <div class="insurer-results">
        <?php echo $results['html']; ?>
    </div>
    <script>
        jQuery(function($){
            $('.insurer-results-count').text('Showing <?php echo $results['shown_count']; ?> of <?php echo $results['total_count']; ?> results');
        });
    </script>
	
	<button class="load-more-insurers"
		style="<?php echo ($results['total_count'] > $results['shown_count']) ? 'display:block;' : 'display:none;' ?>"
	>
		<span>Show More</span>
		<div class="ica-spinner"></div>
	</button>
	
<?php
    return ob_get_clean();
}

// AJAX handler for insurer instant search and load more
function ica_insurer_search_ajax() {
    $params = [
        'search' => $_POST['search'] ?? '',
        'category' => $_POST['category'] ?? 0,
        'sort_by' => $_POST['sort_by'] ?? '',
        'paged' => $_POST['paged'] ?? 1,
        'per_page' => 10,
        'force_category' => $_POST['term_id'] ?? 0,
        'distribution_method' => $_POST['distribution_method'] ?? 'direct',
    ];
    $results = ica_get_insurer_query_results($params);

    wp_send_json([
        'html' => $results['html'],
        'has_more' => $results['has_more'],
        'total_count' => $results['total_count'],
        'shown_count' => $results['shown_count'],
    ]);
}
add_action('wp_ajax_ica_insurer_search', 'ica_insurer_search_ajax');
add_action('wp_ajax_nopriv_ica_insurer_search', 'ica_insurer_search_ajax');

// AJAX handler for insurer/category suggestions - REMOVED (now using JavaScript-only approach)

/**
 * Renders the insurers notice block shortcode
 * 
 * @return string HTML output of the notice blocks
 */
function ica_insurers_notice_block() {
	$broker_notice = get_field('broker_assistance_notice', 'option');
	$disclaimer_notice = get_field('disclaimer_notice', 'option');
	ob_start();
?>
	<div class="insurers-notice-wrapper">
		<?php if (!empty($broker_notice)): ?>
			<div class="broker-notice notice-block"><?php echo $broker_notice ?></div>
		<?php endif; ?>
		<?php if (!empty($disclaimer_notice)): ?>
			<div class="disclaimer-notice notice-block"><?php echo $disclaimer_notice ?></div>
		<?php endif; ?>
	</div>
<?php
    return ob_get_clean();
}
add_shortcode( 'insurers_notice', 'ica_insurers_notice_block' );

/**
 * Renders the insurers categories list with toggle functionality
 * 
 * @return string HTML output of the categories list with parent/child toggle
 */
function ica_insurer_categories_list() {
    $parent_terms = get_terms(array(
        'taxonomy' => 'insurer-category',
        'hide_empty' => false,
        'parent' => 0
    ));

    ob_start();
?>
    <div class="insurers-categories-list">
        <?php if (!empty($parent_terms) && !is_wp_error($parent_terms)): ?>
            <div class="category-toggle-container">
                <?php foreach ($parent_terms as $parent_term): 
                    $child_terms = get_terms(array(
                        'taxonomy' => 'insurer-category',
                        'hide_empty' => false,
                        'parent' => $parent_term->term_id
                    ));
                ?>
                    <div class="category-group">
                        <button class="btn-toggle-category" data-term-id="<?php echo $parent_term->term_id; ?>">
                            <a href="<?php echo esc_url(get_term_link($parent_term)); ?>"><?php echo esc_html($parent_term->name); ?></a>
                            <span class="icon"><i class="fa fa-angle-down"></i></span>
                        </button>
                        <div class="category-children" style="display: none;">
                            <?php 
                            $term_names = array();
                            if (!empty($child_terms) && !is_wp_error($child_terms)) {
                                foreach ($child_terms as $child_term) {
                                    $parent_link = get_term_link($parent_term);
                                    $term_names[] = '<a href="' . esc_url(add_query_arg('category', $child_term->term_id, $parent_link)) . '">' . esc_html($child_term->name) . '</a>';
                                }
                            } else {
                                $parent_link = get_term_link($parent_term);
                                $term_names[] = '<a href="' . esc_url(add_query_arg('category', $parent_term->term_id, $parent_link)) . '">' . esc_html($parent_term->name) . '</a>';
                            }
                            echo implode(', ', $term_names);
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode( 'insurer_categories_list', 'ica_insurer_categories_list' );
