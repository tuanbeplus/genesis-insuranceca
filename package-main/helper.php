<?php
use ScssPhp\ScssPhp\Compiler;
use Smalot\PdfParser\Parser;

if( ! function_exists( 'package_main_scss_compiler' ) ) {
    /**
     * Scss Compiler
     *
     * @param string $in
     * @param string $out
     * @param string $import_path
     * @param string $formatter (default: ScssPhp\ScssPhp\Formatter\Compressed)
     * @param boolean $source_map (SOURCE_MAP_INLINE)
     *
     * @return void
     */
    function package_main_scss_compiler( $scss_string, $out, $import_path = '', $formatter = 'ScssPhp\ScssPhp\Formatter\Compressed', $source_map = false ) {
				$scss = new Compiler();
        if( ! empty( $import_path ) ) $scss->setImportPaths( $import_path );
        if( ! empty( $formatter ) ) $scss->setFormatter( $formatter );
        if( true == $source_map ) $scss->setSourceMap( Compiler::SOURCE_MAP_INLINE );
        file_put_contents( $out, $scss->compile( $scss_string ) );
    }
}

//add SVG to allowed file uploads
function package_main_types_to_uploads($file_types){

    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $new_filetypes );

    return $file_types;
}


//Search default
function header_top_right_widget() {
   ?>
   <div class="header-search">
     <i class="fa fa-search" aria-hidden="true"></i>
   </div>
   <?php
}

// popups share social
function share_page_button(){
    $active = get_field('show_or_hidden_button_share_page_ins');
    $ctaShare = get_field('cta_popus_share_page', 'option');
    $headingPopup = get_field('heading_popus_share_page', 'option');
    $headingLinkCurrent= get_field('heading_page_link_current', 'option');
    if ($active || is_singular('resources')) { ?>
        <div id="insuranceca-share-page" class="bt-share-page">
            <div class="cta-share">
                <?php if ($ctaShare['name'] or $ctaShare['icon']): ?>
                        <?php if ($ctaShare['name']): ?>
                            <span> <?php echo $ctaShare['name'] ?> </span>
                        <?php endif; ?>
                        <?php if ($ctaShare['icon']): ?>
                            <img src="<?php echo $ctaShare['icon'] ?>" alt="icon-share">
                        <?php endif; ?>
                <?php else: ?>
                    <span>Share</span>
                <?php endif; ?>
            </div>
            <div class="content-share-page" style="display: none">
                <?php if ($headingPopup): ?>
                    <h2 class="title-popup"> <?php echo $headingPopup ?> </h2>
                <?php endif; ?>
                <div class="cta-close"></div>
                <?php echo do_shortcode('[easy-social-share counters=0 noaffiliate="no" sidebar="no"  float="no" postfloat="no" topbar="no" bottombar="no" point="no" mobilebar="no" mobilebuttons="no" mobilepoint="no"]');  ?>
                <div class="page-link-current">
                    <div class="__content-inner">
                        <?php if ($headingLinkCurrent): ?>
                            <h4> <?php echo $headingLinkCurrent ?> </h4>
                        <?php endif; ?>
                        <div class="item-page-link">
                            <?php
                            $idPage = get_the_ID();
                            $link_page_current = get_page_link($idPage);
                            ?>
                            <input type="text" id="link-page-current" name="" value="<?php echo $link_page_current; ?>">
                            <div class="meta-popups">
                                <img class="cta-copy" src="<?php echo PJ_URI;?>/assets/images/icon-copy.svg" alt="copy">
                                <span class="tooltip-popup"> copy url </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
}


//Social
function ins_socials_template(){
  $socials = get_field('header_socials','options');
  $fai_landing = get_field('find_an_insurer_landing', 'option');
  $fai_landing = !empty($fai_landing) ? $fai_landing : '/find-an-insurer/';
  if(!empty($socials) || !empty($fai_landing)):
    ?>
    <div class="header-socials">
        <div class="wrap">
          <a class="fai-landing" href="<?php echo esc_url($fai_landing) ?>">Find an Insurer</a>
          <?php foreach ($socials as $key => $social) {
              ?> <a href="<?php echo $social['link']; ?>" target="_blank"> <img src="<?php echo $social['icon']; ?>" alt=""> </a> <?php
          } ?>
        </div>
    </div>
    <?php
  endif;
}

//Search
function insuranceca_search_default_template(){
  ?>
  <div id="ins_form_search" class="ins-search-form-default mfp-hide">
      <div class="top-search">
        <img src="<?php echo PJ_URI; ?>assets/images/logo-green.svg" alt="logo">
      </div>
      <div class="template-search">
        <?php echo do_shortcode(get_field('search_nav','options')); ?>
      </div>
  </div>
  <?php
}

//Top footer
function insuranceca_footer_top_template($output){
  ob_start();
  $enable_footer_top = get_field('enable_footer_top','options');
  if($enable_footer_top):
    $logo_footer        = get_field('logo_footer','options');
    $description_footer = get_field('description_footer','options');
    $subscribe_footer   = get_field('subscribe_footer','options');
    $visit_footer   = get_field('visit_footer','options');
    ?>
    <div class="footer-top">
      <div class="wrap">
          <div class="footer-top--left">
            <?php if($logo_footer): ?>
              <img src="<?php echo $logo_footer; ?>" alt="">
            <?php endif; ?>
            <?php if($description_footer): ?>
              <div class="footer-description">
                  <?php echo $description_footer; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="footer-top--right">
              <div class="item-info subscribe-footer">
                 <h3><?php echo $subscribe_footer['heading']; ?></h3>
                 <div class="des-footer">
                    <div class="__des">
                      <?php echo $subscribe_footer['description']; ?>
                    </div>
                    <div class="__btn">
                      <a href="<?php echo $subscribe_footer['button']['link']; ?>"><?php echo $subscribe_footer['button']['label']; ?><i class="fa fa-angle-right" ></i></a>
                    </div>
                 </div>
              </div>
              <div class="item-info visit-footer">
                 <h3><?php echo $visit_footer['heading']; ?></h3>
                 <div class="des-footer">
                    <div class="__des">
                      <?php echo $visit_footer['description']; ?>
                    </div>
                    <div class="__btn">
                      <a href="<?php echo $visit_footer['button']['link']; ?>"><?php echo $visit_footer['button']['label']; ?><i class="fa fa-angle-right" ></i></a>
                    </div>
                 </div>
              </div>
          </div>
      </div>
    </div>
    <?php
  endif;
  $footer_top = ob_get_clean();
  return $footer_top.$output;
}

// alert banner top page
function alert_banner_top(){
    $active_banner = get_field('show_or_hiden_alert_banner_top');
    $heading_banner = get_field('heading_alert_banner_insuranceca');
    $content_banner = get_field('content_alert_banner_insuranceca');
    if ($active_banner) { ?>
        <div id="bt-alert-banner-top" class="alert-banner-top">
            <div class="conatiner-alert-banner-top">
                <div class="meta-banner">
                    <?php if ($heading_banner): ?>
                        <h2 class="heading-banner"> <?php echo $heading_banner ?> </h2>
                    <?php endif; ?>
                    <?php if ($content_banner): ?>
                        <div class="content-banner"> <?php echo $content_banner ?>  </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cta-close">
                <img src="<?php echo PJ_URI;?>/assets/images/icon-cancel-white.svg" alt="copy">
            </div>
        </div>
    <?php
    }
}

//Change default length text
function insuranceca_excerpt_length_text(){
  return 40;
}

//Save post
$field1 = get_field_object('upload_file');
print_r($field1);
function acf_save_resources($post_id){
  if(get_post_type($post_id) != 'resources') return;
  $acf = $_POST['acf'];
  $old_val      = get_field('upload_file',$post_id);
  $old_val2     = get_field('content_file',$post_id);
  $old_val3     = get_field('link_html',$post_id);
  $old_val4     = get_field('select_type_resources',$post_id);
  $field1       = get_field_object('upload_file');
  $field2       = get_field_object('content_file');
  $field3       = get_field_object('select_type_resources');
  $field4       = get_field_object('link_html');
  $key1 = 'field_6073f8a89e05c';
  $key2 = 'field_608a54d6f0770';
  $key3 = 'field_609257c677695';
  $key4 = 'field_6092597277696';
  $key          = (isset($field1['key'])) ? $field1['key'] : $key1;
  $file_id      = (isset($acf[$key])) ? $acf[$key] : $acf[$key1];
  $type_sources = (isset($acf[$field3['key']])) ? $acf[$field3['key']] : $acf[$key3];
  $key_content  = (isset($field2['key'])) ? $field2['key'] : $key2;
  // if(isset($type_sources) && $type_sources == 'HTML'){
  //   $link_html = (isset($acf[$field4['key']])) ? $acf[$field4['key']] : $acf[$key4];
  //   if($link_html != $old_val3){
  //     if($link_html){
  //       $_POST['acf'][$key_content] = file_get_contents($link_html);
  //     }else{
  //       $_POST['acf'][$key_content] = '';
  //     }
  //   }else{
  //     if(($link_html && trim($old_val2) == '') || ($link_html && $old_val4 != $type_sources)){
  //       $_POST['acf'][$key_content] = file_get_contents($link_html);
  //     }
  //   }
  // }else{
    if($file_id != $old_val['ID']){
      if($file_id){
        $file = get_attached_file($file_id);
        $content = parsePDF($file);
        $_POST['acf'][$key_content] = $content;
      }else{
        $_POST['acf'][$key_content] = '';
      }
    }else{
      if(($file_id && trim($old_val2) == '') || ($file_id && $old_val4 != $type_sources)){
        $file = get_attached_file($file_id);
        $content = parsePDF($file);
        $_POST['acf'][$key_content] = $content;
      }
    }
  //}
}

//Parser
function parsePDF($filename)
{
    //Parse pdf file and build necessary objects.
     $parser = new Parser();
     try{
        $pdf = $parser->parseFile($filename);
      }catch(ParseError $p){
        wp_die($p->getMessage());
      }
     $text = $pdf->getText();
     return $text;
}

//Style admin
add_action('admin_head', 'my_custom_fonts');
function my_custom_fonts() {
  echo '<style>div[data-name="content_file"]{display:none;}</style>';
}




// Add the custom columns order to the team post type:
add_filter( 'manage_team_posts_columns', 'set_custom_edit_team_columns' );
add_filter( 'manage_ins-faqs_posts_columns', 'set_custom_edit_team_columns' );
function set_custom_edit_team_columns($columns) {
    // unset( $columns['author'] );
    $columns['team_order'] = __( 'Order', 'your_text_domain' );
    return $columns;
}

// get data order columns
add_action( 'manage_team_posts_custom_column' , 'get_order_column', 10, 2 );
add_action( 'manage_ins-faqs_posts_custom_column' , 'get_order_column', 10, 2 );
function get_order_column( $column, $post_id ) {
    switch ( $column ) {

        case 'team_order' :

            // echo get_post_meta( $post_id , 'page-attributes' , true );

            $team = get_post($post_id);
            echo $team ->menu_order;

            break;

    }
}


{

  //Update data resources
  add_action('init','update_data_resource');
  function update_data_resource(){
    if(isset($_GET['update-resources'])){
      $args = array(
  			'post_type' => 'resources',
  			'post_status' => 'publish',
  			'posts_per_page' => -1,
        'wp_title' => 'Q:'
  		);
      add_filter( 'posts_where', 'like_title_posts_where', 10, 2 );
      $the_query = new WP_Query($args);
      remove_filter( 'posts_where', 'like_title_posts_where', 10, 2 );
      // The Loop
      if ( $the_query->have_posts() ) {
          while ( $the_query->have_posts() ) {
              $the_query->the_post();
              wp_set_post_terms( get_the_ID(), array(50), 'ins-type');
          }
      } else {
          // no posts found
      }
      /* Restore original Post Data */
      wp_reset_postdata();
    }
  }
  //add_filter( 'posts_where', 'like_title_posts_where', 10, 2 );
  function like_title_posts_where( $where, &$wp_query )
  {
      global $wpdb;
      if ( $wp_title = $wp_query->get( 'wp_title' ) ) {
          $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'' . esc_sql( $wpdb->esc_like( $wp_title ) ) . '%\'';
      }
      return $where;
  }

  /* Import PDF to Resources*/
  add_action('template_include','load_template_import', 999 );
  function load_template_import($template){
    if(isset($_GET['action']) && $_GET['action'] == 'import'){
      $type = $_GET['type'];
      if($type){
        $template = locate_template( array( 'template-import-resources.php' ) );
      }else{
        wp_die('Please chosen for type Resources to import: "&type=[Type Resources]"');
      }
    }
    return $template;
  }

  //Import data
  add_action('init','run_import_resources');
  function run_import_resources(){
  global $error_import;
  if(isset($_POST['action-import']) && isset($_FILES['file-import'])){

    require_once PJ_DIR."lib/SimpleXLSX.php";

    $type = $_GET['type'];
    $upload_dir = wp_upload_dir();
    $file = $_FILES['file-import'];
    $resources_dir = $upload_dir['basedir'].'/resources/'.$type.'/';
    if ( $xlsx = SimpleXLSX::parse($file['tmp_name']) ) {

    $month_arr = array(
      'Jan' => '01',
      'Feb' => '02',
      'Mar' => '03',
      'Apr' => '04',
      'May' => '05',
      'Jun' => '06',
      'Jul' => '07',
      'Aug' => '08',
      'Sept' => '09',
      'Sep' => '09',
      'Oct' => '10',
      'Nov' => '11',
      'Dec' => '12'
    );

          $yearimport = isset($_GET['year']) ? $_GET['year'] : '';
          $file_tmp = array();
          $count = 1;
          $list_import = [];
          foreach( $xlsx->rows() as $k => $r) {

            //get year
              if($r[1] == 'Numbered File Name Prefix'){
                 $year = $r[0];
                 continue;
              }

              $date = $r[2];
              $timestamp = strtotime(str_replace('/', '-', $date));

              if($date != 'Date'){
                $year_res = date('Y',$timestamp);
                if($year_res != $year){
                  $year = $year_res;
                }
              }

              if($yearimport && $yearimport != $year) continue;

                //if($year == $yearimport){

                  //Check data empty
                  //Get month
                  $month = (trim($r[0]) != '') ? $month_arr[$r[0]] : $month;

                  if(trim($r[1]) == '') continue;
                  $pdf_name = $r[1]; //PDF name
                  $title = $r[3]; //Title
                  if($type == 'ICA reports'){
                    $dir_path_file = $resources_dir.$year;
                  }else {
                    $dir_path_file = $resources_dir.$year.'/'.$year.'_'.$month;
                  }

                  $name_file = '';
                  $path_file = '';
                  $content = '';

                  //Find file in folder
                  $files = scandir($dir_path_file);
                  //print_r($files);echo '<br>';
                  foreach ($files as $f) {
                    if (($pdf_name == $f || $pdf_name.'.pdf' == $f || strtolower($pdf_name) == strtolower($f)) && $f != '.' && $f != '..') {
                       $name_file = $f;
                       $list_import[] = $f;
                    }

                    if($f && $f != '.' && $f != '..' && !in_array($f,$file_tmp)){
                       // echo ($count).'.'.$f;
                       // echo '<br>';
                      $file_tmp[] = $f;
                      //$count++;
                    }

                  }

                  // Get path file
                  if($name_file){
                   $path_file = $dir_path_file.'/'.$name_file;
                  }

                  //Get content file PDF
                  if($path_file){

                    if($pdf_name != '201505_Best Practice Workers Compensation Scheme'
                        && $pdf_name != '202007_ICA_insuringForPandemics'
                        && $pdf_name != '2017_02_Effective Disclosure Research Report'
                      ){
                      $parser = new Parser();
                      $pdf = $parser->parseFile($path_file);
                      $content = $pdf->getText();
                    }

                    //add file to Media
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                    require_once(ABSPATH . 'wp-admin/includes/post.php');

                    $upload_folder = $upload_dir['path'];

                    // Set filename, incl path
                    if($type == 'ICA reports'){
                      $filename = 'resources/'.$type.'/'.$year.'/'.$name_file;
                    }else {
                      $filename = 'resources/'.$type.'/'.$year.'/'.$year.'_'.$month.'/'.$name_file;
                    }

                    // Check the type of file. We'll use this as the 'post_mime_type'.
                    $filetype = wp_check_filetype( basename( $filename ), null );

                    // Get file title
                    $title_file = preg_replace( '/\.[^.]+$/', '', basename( $name_file ) );

                    // Prepare an array of post data for the attachment.
                    $attachment_data = array(
                      'guid'           => $upload_dir['url'] . '/' . basename( $filename ),
                      'post_mime_type' => $filetype['type'],
                      'post_title'     => $title_file,
                      'post_content'   => '',
                      'post_status'    => 'inherit'
                    );

                    // Does the attachment already exist ?
                    if( post_exists( $title_file ) ){
                      $attachment = get_page_by_title( $title_file, OBJECT, 'attachment');
                      if( !empty( $attachment ) ){
                        $attachment_data['ID'] = $attachment->ID;
                      }
                    }

                    // If no parent id is set, reset to default(0)
                    if( empty( $parent_id ) ){
                      $parent_id = 0;
                    }

                    // Insert the attachment.
                    $attach_id = wp_insert_attachment( $attachment_data, $filename, $parent_id );

                    // Generate the metadata for the attachment, and update the database record.
                    $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
                    wp_update_attachment_metadata( $attach_id, $attach_data );

                    if(!trim($check_resources = post_exists($title ,'','','resources','publish'))){
                      // Create post object
                      $resources = array(
                        'post_title'    => $title,
                        'post_status'   => 'publish',
                        'post_author'   => 1,
                        'post_date'     => date('Y-m-d',$timestamp),
                        'post_type'	  => 'resources'
                      );
                      // Insert the post into the database
                      $res_id = wp_insert_post( $resources );
                    }else{
                      $res_id = $check_resources;
                    }


                    echo $count.'. '.$res_id.' - '.$title. ' - '. get_post_type($res_id);
                    echo '<br>';
                    $count++;

                    $featured_id = get_field('featured_image_import','options');

                    //Update data
                    update_field('upload_file',$attach_id,$res_id);
                    update_field('content_file',$content,$res_id);
                    wp_set_object_terms( $res_id, strtolower($type) , 'ins-type' );
                    set_post_thumbnail( $res_id, $featured_id );

                  }
                //}
          }

          $list_diff = array_diff($file_tmp,$list_import);

          $f = 1;
          echo 'DIFF:<br>';
          foreach ($list_diff as $key => $fk) {
            echo $f.'. '.$fk;
            echo '<br>';
            $f++;
          }

          } else {
            $error_import = SimpleXLSX::parseError();
          }
        }
      }
}
