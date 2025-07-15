<?php
/**
 * Defined
 */
define( 'PJ_DIR', get_stylesheet_directory() . '/package-main/' );
define( 'PJ_URI', get_stylesheet_directory_uri() . '/package-main/' );
define( 'PJ_VERSION', time() );
define( 'PJ_DEV_MODE', true ); // enable to compiler scssphp

/**
 * Includes
 */
require( PJ_DIR . 'vendor/autoload.php' );
require( PJ_DIR . 'lib/vendor/autoload.php' );
require( PJ_DIR . 'custom-post-type.php' );
require( PJ_DIR . 'hooks.php' );
require( PJ_DIR . 'helper.php' );
require( PJ_DIR . 'ajax.php' );
require( PJ_DIR . 'static.php' );
require( PJ_DIR . 'options.php' );
require( PJ_DIR . 'shortcodes.php' );
