<?php

add_action('init', 'package_main_acf_init');
function package_main_acf_init() {
	if( function_exists('acf_add_options_page') ) {
		if( current_user_can( 'administrator' ) ):
			acf_add_options_page(array(
				'page_title' 	=> __('Theme Options', 'package-main'),
				'menu_title' 	=> __('Theme Options', 'package-main'),
				'menu_slug' 	=> 'ica-options',
				'position'      => 50
			));
		endif;
	}
}
