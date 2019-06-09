<?php


class ORBIT_FEP extends ORBIT_BASE{

  function __construct(){
    add_filter( 'orbit_post_type_vars', array( $this, 'createPostType' ) );

    add_filter( 'orbit_meta_box_vars', array( $this, 'createMetaBox' ) );

    // SEPERATE METABOX FOR FILTERS ONLY
    add_action( 'orbit_meta_box_html', array( $this, 'metaboxForFEP' ), 1, 2 );

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

    add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

    add_action( 'save_post', array( $this, 'save_post' ) );

    //SHORTCODE
    add_shortcode( 'orbit_fep', array( $this, 'shortcode' ) );
  }

  // Callback Functions
  function createPostType( $post_types ){
    $post_types['orbit-fep'] = array(
      'slug' 		=> 'orbit-fep',
      'labels'	=> array(
        'name' 					=> 'Orbit Fep',
        'singular_name' => 'Orbit Fep',
      ),
      // 'rewrite'		=> array('slug' => 'incidents', 'with_front' => false ),
      'public'		=> true,
      'supports'	=> array( 'title', 'editor' )
    );
    return $post_types;
  }

  function createMetaBox( $meta_box ){
    global $post_type;

    //POST STATUS
    $status = array();
    $post_stats = get_post_statuses();

    foreach( $post_stats as $present_status ){
      // echo $status;
      array_push( $status, $present_status  );
    }


    if( 'orbit-fep' != $post_type ) return $meta_box;

    $meta_box['orbit-fep'] = array(
      array(
        'id'		=> 'orbit-fep-pages',
        'title'		=> 'Orbit Form Fields',
        'fields'	=> array()
      ),
      array(
        'id'      =>  'orbit-fep-settings',
        'title'   =>  'Orbit Fep Settings',
        'fields'  =>  array(
          'posttypes' => array(
            'type' 		=> 'dropdown',
            'text' 		=> 'Select Post Types',
            'options'	=> array()
          ),
          'post_status' => array(
            'type' 		=> 'dropdown',
            'text' 		=> 'Select Post Status',
            'options'	=> $post_stats
          ),
        )
      ),
    );
    return $meta_box;
  }

  function metaboxForFEP( $post, $box ){
    if( isset( $box['id'] ) && 'orbit-fep-pages' == $box['id'] ){
      $orbit_filter = ORBIT_FILTER::getInstance();

      // FORM ATTRIBUTES THAT IS NEEDED BY THE REPEATER FILTERS
      $form_atts = $orbit_filter->vars();
      if( !$form_atts || !is_array( $form_atts ) ){ $form_atts = array(); }
      $form_atts['tax_types'] = get_taxonomies();

      $form_atts['db'] = $this->getDBData( $post->ID );

      //ADD A NEW TYPE INTO THE TYPES ARRAY
      $new_type = array(
        'post' => 'Post',
        'cf'   => 'Custom Fields'
      );
      foreach( $new_type as $slug_type => $value_type ){
        $form_atts['types'][$slug_type] = $value_type;
      }
      unset( $form_atts['types']['postdate'] );

      //WHEN TYPE IS POST
      $form_atts['post_types'] = array(
        'title'     =>  'Title',
        'content'   =>  'Description',
        'date'      =>  'Date'
      );

      //NEW FORM FIELDS
      $new_field = array(
        'radio'     =>  'Radio (multiple)',
        'text'      =>  'Text',
        'textarea'  =>  'Textarea'
      );
      foreach( $new_field as $slug_type => $value_type ){
        $form_atts['forms'][$slug_type] = $value_type;
      }

      // echo '<pre>';
      // print_r( $form_atts );
      // echo '<pre>';

      // TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
      _e( "<div data-behaviour='orbit-fep-pages' data-atts='".wp_json_encode( $form_atts )."'></div>");// data-atts='".wp_json_encode( $form_atts )."'
      //_e( "<div data-behaviour='orbit-fep-repeater'></div>");
      //_e( "<div data-behaviour='orbit-fep-options-repeater'></div>");
    }
  }

  // GET THE FEP FORMS DATA STORED AS ARRAY IN POST META
  function getDBData( $post_id ){
    $data = get_post_meta( $post_id, 'fep', true );
    if( $data && is_array( $data ) ){
      return $data;
    }
    return array();
  }


  /*
  * TRIGGERED WHEN THE PUBLISH/UPDATE BUTTON IS CLICKED IN THE ADMIN PANEL
  * THIS IS WHERE THE FILTERS THAT ARE ADDED BY THE USER FROM THE ADMIN PANEL IS SAVED IN THE DB
  */
  function save_post( $post_id ){
    $post_type = get_post_type( $post_id );
    if ( "orbit-fep" != $post_type ) return;

    // SAVE FILTERS IN POST META
    if( isset( $_POST['fep'] ) && is_array( $_POST['fep'] ) ){

      // SORT ARRAY BY THE VALUE ORDER
      $byOrder = array_column( $_POST['fep'], 'rank');
      array_multisort( $byOrder, SORT_ASC, $_POST['fep'] );

      // SAVE
      update_post_meta( $post_id, 'fep', $_POST['fep'] );
    }
    //wp_die();
  }

  function shortcode( $atts ){

    $atts = shortcode_atts( array(
      'id'      =>  '0'     // POST ID
    ), $atts, 'fep-form' );

    ob_start();

    // GET FORM PAGES INFORMATION FROM THE METADATA IN ORBIT-FEP
    $fep_pages = $this->getDBData( $atts['id'] );

    $new_post = array(
      'post_type'   => get_post_meta( $atts['id'], 'posttypes', true ),
      'post_status' => get_post_meta( $atts['id'], 'post_status', true )
    );

    $this->create( $fep_pages, $new_post );

    return ob_get_clean();
  }

  function create( $pages, $new_post = array() ){

    // INSERT POST ONCE THE FORM HAS BEEN SUBMITTED
    if( $_POST ){ $this->insertPost( $new_post ); }

    // STARTING OF FORM TAG
    echo "<form class='soah-fep' method='post' enctype='multipart/form-data'>";

    // USING THE ORBIT MULTIPART FORM TO CREATE SLIDES
    $orbit_multipart_form = ORBIT_MULTIPART_FORM::getInstance();
    $orbit_multipart_form->create( $pages );

    // END OF FORM TAG
    echo "</form>";
  }

  function insertPost( $post_info ){

    // ADD POST RELATED INFORMATION TO AN ARRAY
    $post_fields_arr = array( 'post_title', 'post_content', 'post_date' );
    foreach( $post_fields_arr as $post_field ){
      if( isset( $_POST[ $post_field ] ) ){
        $post_info[ $post_field ] = $_POST[ $post_field ];
      }
    }

    $post_id = wp_insert_post( $post_info );

    if( $post_id ){
      // ONLY IF POST ID IS VALID - ensures that the above insert was successfull
      foreach( $_POST as $slug => $value ){
        if( strpos( $slug, 'tax_') !== false ){
          // ADDING TERMS TO THE NEW POST
          $taxonomy = str_replace( "tax_", "", $slug );
          wp_set_post_terms( $post_id, $value, $taxonomy );
        }
        elseif( strpos( $slug, 'cf_') !== false ){
          // ADDING CUSTOM META VALUES TO THE POST
          $meta_name = str_replace( "cf_", "", $slug );
          update_post_meta( $post_id, $meta_name, $value );
        }
      }
    } // IF $POST_ID

  } // END OF FUNCTION

  function assets(){
    $orbit_multipart_form = ORBIT_MULTIPART_FORM::getInstance();
    $orbit_multipart_form->enqueue_assets();
  }

  function admin_assets(){
    wp_enqueue_style( 'orbit-form-style', plugin_dir_url( __FILE__ ).'assets/style.css',array(), time() );
    wp_enqueue_script( 'orbit-fep-pages', plugin_dir_url( __FILE__ ).'assets/repeater-pages.js', array('jquery', 'orbit-repeater' ), time(), true );
    wp_enqueue_script( 'orbit-fields', plugin_dir_url( __FILE__ ).'assets/repeater-fields.js', array('jquery', 'orbit-repeater' ), time(), true );
    wp_enqueue_script( 'orbit-options-repeater', plugin_dir_url( __FILE__ ).'assets/repeater-options.js', array('jquery', 'orbit-repeater' ), time(), true );
  }

}//class ends

ORBIT_FEP::getInstance();
