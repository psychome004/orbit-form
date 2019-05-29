<?php


class ORBIT_FEP extends ORBIT_BASE{

  function __construct(){
    add_filter( 'orbit_post_type_vars', array( $this, 'createPostType' ) );

    add_filter( 'orbit_meta_box_vars', array( $this, 'createMetaBox' ) );

    // SEPERATE METABOX FOR FILTERS ONLY
    add_action( 'orbit_meta_box_html', array( $this, 'metaboxHTML' ), 1, 2 );

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
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

    if( 'orbit-fep' != $post_type ) return $meta_box;

    $meta_box['orbit-fep'] = array(
      array(
        'id'		=> 'orbit-fep-pages',
        'title'		=> 'Orbit Form Fields',
        'fields'	=> array()
      ),
    );
    return $meta_box;
  }

  function metaboxHTML( $post, $box ){
    if( isset( $box['id'] ) && 'orbit-fep-pages' == $box['id'] ){
      $orbit_filter = ORBIT_FILTER::getInstance();

      // FORM ATTRIBUTES THAT IS NEEDED BY THE REPEATER FILTERS
      $form_atts = $orbit_filter->vars();
      if( !$form_atts || !is_array( $form_atts ) ){ $form_atts = array(); }
      $form_atts['tax_options'] = get_taxonomies();
      $form_atts['postdate_options'] = array(
        'year'	=>	'Year',
      );
      $form_atts['db'] = array();
      //$form_atts['db'] = $this->getFiltersFromDB( $post->ID );

      // TRIGGER THE REPEATER FILTER BY DATA BEHAVIOUR ATTRIBUTE
      _e( "<div data-behaviour='orbit-fep-pages' data-atts='".wp_json_encode( $form_atts )."'></div>");// data-atts='".wp_json_encode( $form_atts )."'
      _e( "<div data-behaviour='orbit-fep-repeater'></div>");
    }
  }

  function admin_assets(){
    wp_enqueue_style( 'orbit-form-style', plugin_dir_url( __FILE__ ).'assets/style.css',array(), time() );
    wp_enqueue_script( 'orbit-fep-pages', plugin_dir_url( __FILE__ ).'assets/repeater-pages.js', array('jquery', 'orbit-repeater' ), time(), true );
    wp_enqueue_script( 'orbit-fep-form-page', plugin_dir_url( __FILE__ ).'assets/repeater-fep.js', array('jquery', 'orbit-repeater' ), time(), true );
  }



}//construct ends

ORBIT_FEP::getInstance();
