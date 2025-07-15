<?php
 /**
  * Load scripts
  *
  */
  add_action( 'wp_enqueue_scripts', 'package_main_scripts' );
  if( ! function_exists( 'package_main_scripts' ) ) {
      function package_main_scripts() {

        // Fontawesome
        wp_enqueue_style( 'font-awesome', PJ_URI . 'assets/font-awesome/css/font-awesome.min.css', false, '4.7.0' );
        // css
        wp_enqueue_style( 'main-css', PJ_URI . 'assets/css/main.css', false, PJ_VERSION );

        //popup
        wp_enqueue_style( 'magnific-popup', PJ_URI . 'assets/popup/magnific-popup.css', false, PJ_VERSION );
        wp_enqueue_script( 'magnific-popup', PJ_URI . 'assets/popup/jquery.magnific-popup.min.js', ['jquery'], PJ_VERSION, true );

        wp_enqueue_script( 'global-js', PJ_URI . 'assets/js/global.js', ['jquery'], PJ_VERSION, true );
        wp_enqueue_script( 'insuranceca-custom-js', PJ_URI . 'assets/js/custom.js', ['jquery'], PJ_VERSION, true );
        wp_localize_script( 'global-js', 'PJ_Global', apply_filters( 'pj/wp_localize_script/PJ_Global', [
           'ajax_url' => admin_url( 'admin-ajax.php' ),
           'user_info' => wp_get_current_user(),
        ] ) );
        wp_localize_script( 'insuranceca-custom-js', 'ICA_AJAX', apply_filters( 'pj/wp_localize_script/ICA_AJAX', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
        ] ) );
      }
  }

  {
    /**
     * Compiler Scss
     *
     */
    add_action( 'init', function() {
        if( true != PJ_DEV_MODE ) return;
        package_main_scss_compiler(
            file_get_contents( PJ_DIR . 'assets/scss/main.scss' ),
            PJ_DIR . 'assets/css/main.css',
            PJ_DIR . 'assets/scss',
            'ScssPhp\ScssPhp\Formatter\Compressed',
            false
        );
    } );
  }
