<?php
/**
 * Insurer Category Taxonomy Template
 *
 * @package Genesis Insurance CA
 */

get_header();
echo '<main class="content">';

$term = get_queried_object();

ica_insurer_breadcrumbs();

echo '<h1>' . esc_html($term->name) . '</h1>';

if (!empty($term->description)) {
    echo '<div class="term-description">' . wp_kses_post($term->description) . '</div>';
}

// Pass the current category ID to the shortcode
echo do_shortcode('[insurers_instant_search category="' . $term->term_id . '"]');

echo do_shortcode('[insurers_notice]');

echo '</main>';
get_footer();
