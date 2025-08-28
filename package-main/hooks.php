<?php
//Header
{
  //Remove default
  remove_action( 'genesis_site_description', 'genesis_seo_site_description' );
  remove_action( 'genesis_before_header', 'genesis_skip_links', 5 );
  add_action( 'genesis_before_header', 'ins_socials_template', 5 );
}

//Footer
{
  // Remove Footer
  // remove_action('genesis_footer', 'genesis_do_footer');
  // remove_action('genesis_footer', 'genesis_footer_markup_open', 5);
  // remove_action('genesis_footer', 'genesis_footer_markup_close', 15);
  // add_filter('genesis_sidebar_title_output','__return_false');
  add_action('genesis_footer', 'insuranceca_search_default_template',99);
  add_filter('genesis_footer_widget_areas', 'insuranceca_footer_top_template');
}

//* Remove the edit link
add_filter ( 'genesis_edit_post_link' , '__return_false' );

// Support SVG
add_action('upload_mimes', 'package_main_types_to_uploads');

//Menu main
add_action( 'genesis_header', 'genesis_do_nav' , 12 );
add_action( 'genesis_header', 'header_top_right_widget', 12 );

//Change length text default
add_filter( 'excerpt_length', 'insuranceca_excerpt_length_text' , 20 );

//Save post
add_action('acf/save_post', 'acf_save_resources' , 5);


add_action('admin_head', function() {
  ?>
  <style>
    body .postbox .categorydiv div.tabs-panel {
      max-height: 500px;
    }
    body .postbox .categorydiv div.tabs-panel ul li {
      margin-bottom: 2px;
      font-size: 14px;
    }
  </style>
  <?php
});
