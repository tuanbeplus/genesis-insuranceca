<?php
/**
 * Insurer Archive Template
 *
 * @package Genesis Insurance CA
 */
get_header(); 
echo '<main class="content">';

$key_search = $_GET['search'] ?? null;

ica_insurer_breadcrumbs();

echo '<h1>Search result for: "<span class="key-search">'. $key_search .'</span>"</h1>';

echo do_shortcode('[insurers_instant_search]');

echo do_shortcode('[insurers_notice]');

echo '</main>';
get_footer();
