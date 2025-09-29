<?php
/**
 * Genesis Sample.
 *
 * This file adds functions to the Genesis Sample Theme.
 *
 * @package Genesis Sample
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://www.studiopress.com/
 */

// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// Sets up the Theme.
require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

// Load package main
require_once get_stylesheet_directory() . '/package-main/init-load.php';

add_action( 'after_setup_theme', 'genesis_sample_localization_setup' );
/**
 * Sets localization (do not remove).
 *
 * @since 1.0.0
 */
function genesis_sample_localization_setup() {

	load_child_theme_textdomain( genesis_get_theme_handle(), get_stylesheet_directory() . '/languages' );

}

// Adds helper functions.
require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Adds image upload and color select to Customizer.
require_once get_stylesheet_directory() . '/lib/customize.php';

// Includes Customizer CSS.
require_once get_stylesheet_directory() . '/lib/output.php';

// Adds WooCommerce support.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Adds the required WooCommerce styles and Customizer CSS.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Adds the Genesis Connect WooCommerce notice.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

add_action( 'after_setup_theme', 'genesis_child_gutenberg_support' );
/**
 * Adds Gutenberg opt-in features and styling.
 *
 * @since 2.7.0
 */
function genesis_child_gutenberg_support() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- using same in all child themes to allow action to be unhooked.
	require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';
}

// Registers the responsive menus.
if ( function_exists( 'genesis_register_responsive_menus' ) ) {
	genesis_register_responsive_menus( genesis_get_config( 'responsive-menus' ) );
}

add_action( 'wp_enqueue_scripts', 'genesis_sample_enqueue_scripts_styles' );
/**
 * Enqueues scripts and styles.
 *
 * @since 1.0.0
 */
function genesis_sample_enqueue_scripts_styles() {

	$appearance = genesis_get_config( 'appearance' );

	wp_enqueue_style(
		genesis_get_theme_handle() . '-fonts',
		$appearance['fonts-url'],
		[],
		genesis_get_theme_version()
	);

	wp_enqueue_style( 'dashicons' );

	if ( genesis_is_amp() ) {
		wp_enqueue_style(
			genesis_get_theme_handle() . '-amp',
			get_stylesheet_directory_uri() . '/lib/amp/amp.css',
			[ genesis_get_theme_handle() ],
			genesis_get_theme_version()
		);
	}

}

add_action( 'after_setup_theme', 'genesis_sample_theme_support', 9 );
/**
 * Add desired theme supports.
 *
 * See config file at `config/theme-supports.php`.
 *
 * @since 3.0.0
 */
function genesis_sample_theme_support() {

	$theme_supports = genesis_get_config( 'theme-supports' );

	foreach ( $theme_supports as $feature => $args ) {
		add_theme_support( $feature, $args );
	}

}

add_action( 'after_setup_theme', 'genesis_sample_post_type_support', 9 );
/**
 * Add desired post type supports.
 *
 * See config file at `config/post-type-supports.php`.
 *
 * @since 3.0.0
 */
function genesis_sample_post_type_support() {

	$post_type_supports = genesis_get_config( 'post-type-supports' );

	foreach ( $post_type_supports as $post_type => $args ) {
		add_post_type_support( $post_type, $args );
	}

}

// Adds image sizes.
add_image_size( 'sidebar-featured', 75, 75, true );
add_image_size( 'genesis-singular-images', 702, 526, true );

// Removes header right widget area.
unregister_sidebar( 'header-right' );

// Removes secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

// Removes site layouts.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Repositions primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
//add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Repositions the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_footer', 'genesis_do_subnav', 10 );

add_filter( 'wp_nav_menu_args', 'genesis_sample_secondary_menu_args' );
/**
 * Reduces secondary navigation menu to one level depth.
 *
 * @since 2.2.3
 *
 * @param array $args Original menu options.
 * @return array Menu options with depth set to 1.
 */
function genesis_sample_secondary_menu_args( $args ) {

	if ( 'secondary' === $args['theme_location'] ) {
		$args['depth'] = 1;
	}

	return $args;

}

add_filter( 'genesis_author_box_gravatar_size', 'genesis_sample_author_box_gravatar' );
/**
 * Modifies size of the Gravatar in the author box.
 *
 * @since 2.2.3
 *
 * @param int $size Original icon size.
 * @return int Modified icon size.
 */
function genesis_sample_author_box_gravatar( $size ) {

	return 90;

}

add_filter( 'genesis_comment_list_args', 'genesis_sample_comments_gravatar' );
/**
 * Modifies size of the Gravatar in the entry comments.
 *
 * @since 2.2.3
 *
 * @param array $args Gravatar settings.
 * @return array Gravatar settings with modified size.
 */
function genesis_sample_comments_gravatar( $args ) {

	$args['avatar_size'] = 60;
	return $args;

}
//
function create_shortcode_tootip($args, $content) {
        $tootip = $args['content'];
				$bt_text_tootip = $args['text'];
        return '<span tooltip="'.$tootip.'">'.$bt_text_tootip.'</span>';
}
add_shortcode( 'bt_tooltip', 'create_shortcode_tootip' );

/**
 * Additional Information into headers.
 */
function additional_information_into_headers() {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    header('X-Frame-Options: SAMEORIGIN');
    // Update the Content-Security-Policy header, add Crazy Egg
    header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: *.crazyegg.com blob:;");
    header('X-Content-Type-Options: nosniff');
    header('Permissions-Policy: vibrate=()');
    header('Referrer-Policy: no-referrer-when-downgrade');
}
add_action( 'send_headers', 'additional_information_into_headers' );

// change content for PDF RESOURCE 
function bt_default_pdf_resource_content($content) {
	global $post;
	// Ensure $post is set and is an object
	if ( ! isset( $post ) || ! is_object( $post ) ) {
		return $content;
	}
	$post_id = isset( $post->ID ) ? $post->ID : 0;
	$post_type = isset( $post->post_type ) ? $post->post_type : '';

	if ( ! $post_id || $post_type !== 'resources' ) {
		return $content;
	}
	$select_type_resources = get_field('select_type_resources', $post_id);
	$upload_file = get_field('upload_file', $post_id);

	if ( $upload_file && is_singular('resources') && $select_type_resources == 'PDF' ) {
		ob_start(); ?>
		<div class="container">
			<div class="resources_document" style="padding: 20px 0 40px;">
				<h3>Document:</h3>
				<a target="_blank" style="display: flex;color: #2f2f39;align-items: center;" href="<?php echo esc_url($upload_file['url']); ?>"><img src="/wp-content/plugins/elementor-addons/assets/images/Bitmap-pdf.svg" alt="icon" style="margin-right: 10px;"><?php echo esc_html($upload_file['name']); ?></a>
			</div>
		</div>
		<?php
		$content .= ob_get_clean();
	}
	return $content;
}
add_filter('the_content', 'bt_default_pdf_resource_content');

// Hide all tax archive of Resource post type
add_action('template_redirect', function () {
    if (is_tax(['ins-type', 'ins-topic'])) {
        wp_redirect(home_url('/404'), 301);
        exit;
    }
});

/**
 * Outputs breadcrumbs for insurer post type pages
 * 
 * Displays a breadcrumb trail for insurer single posts, archive, and taxonomy pages
 * Format: Home > Find an Insurer > [Current Page]
 * 
 * @return void Outputs HTML breadcrumbs
 */
function ica_insurer_breadcrumbs() {
    // Settings
    $separator = '<span><i class="fa fa-angle-right"></i></span>';
    $home_title = 'Home';
    $landing = get_field('find_an_insurer_landing', 'option');
    $landing = !empty($landing) ? $landing : '/find-an-insurer/';
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';

    // Start the breadcrumb with a link to the home page
    echo '<div class="ica-breadcrumbs">';
    echo '<a href="' . get_home_url() . '">' . $home_title . '</a> ' . $separator . ' ';
    // Add "Find an Insurer" archive link
    echo '<a href="' . esc_url($landing) . '">Find an Insurer</a> ' . $separator . ' ';
    if (is_singular('insurer')) {
        // Current insurer title
        echo '<span>' . get_the_title() . '</span>';
    } elseif (is_tax('insurer-category')) {
        // Current category name
        $term = get_queried_object();
        echo '<span>' . $term->name . '</span>';
    } elseif (is_post_type_archive('insurer')) {
        // For insurer archive
        echo '<span>Search result for: "<span class="key-search">' . esc_html($search) . '</span>"</span>';
    }
    echo '</div>';
}

