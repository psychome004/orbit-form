<?php


class ORBIT_FEP extends ORBIT_BASE{

  function __construct(){
    add_filter( 'orbit_post_type_vars', array( $this, 'createPostType' ) );

    add_filter( 'orbit_meta_box_vars', array( $this, 'createMetaBox' ) );

    // SEPERATE METABOX FOR FILTERS ONLY
    add_action( 'orbit_meta_box_html', array( $this, 'metaboxHTML' ), 1, 2 );

    add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

    add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );

    add_action( 'save_post', array( $this, 'save_post' ) );

    //SHORTCODE
    add_shortcode( 'fep-form', array( $this, 'addForm' ) );
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
      array(
        'id'      =>  'orbit-fep-settings',
        'title'   =>  'Orbit Fep Settings',
        'fields'  =>  array(
          'posttypes' => array(
            'type' 		=> 'dropdown',
            'text' 		=> 'Select Post Types',
            'options'	=> array()
          ),
          'posts_status' => array(
            'type' 		=> 'dropdown',
            'text' 		=> 'Select Post Status',
            'options'	=> array( )
          ),
        )
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

      //POST STATUS
      $form_atts['post_status'] = get_post_statuses();

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
      _e( "<div data-behaviour='orbit-fep-repeater'></div>");
      _e( "<div data-behaviour='orbit-fep-options-repeater'></div>");
    }
  }

  // copied

  // GET THE FILTERS STORED AS ARRAY IN POST META
  function getDBData( $post_id ){
    $filtersFromDB = get_post_meta( $post_id, 'fep', true );
    if( $filtersFromDB && is_array( $filtersFromDB ) ){
      return $filtersFromDB;
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

  function addForm( $atts ){
    $atts = shortcode_atts( array(
      'id'      =>  '0'
    ), $atts, 'fep-form' );

    $post_data = get_post_meta( $atts[id],'fep',true );

    //INSERTS THE POST INTO THE SELECTED POST TYPE
    $post_type = get_post_meta( $atts[id],'posttypes',true );

    if( isset( $post_type ) ){
      $this->insert_posts( $post_type );
    }
    //echo "<pre>";
    //print_r( $post_type );
    //echo "</pre>";

    echo "<form class='soah-fep'  method='post' enctype='multipart/form-data'>";

    // Create Sections using post_data array

    $orbit_multipart_form = ORBIT_MULTIPART_FORM::getInstance();
    $orbit_multipart_form->create( count( $post_data ), function( $i, $post_data ){

      $this->display_inline_section( $post_data[ $i ] );
      //print_r( $post_data[ $i ] );


      //echo 'hello'.$i;
    }, $post_data );

    echo "</form>";
  }

  function insert_posts( $post_type ){

    if( $_POST ){


      // echo "<pre>";
      // print_r( $_POST );
      // echo "</pre>";

      // POST INFORMATION ARRAY
      $post_info = array(
        'post_type' => $post_type
      );

      $post_fields_arr = array( 'post_title', 'post_content', 'post_date', 'post_status' );

      foreach( $post_fields_arr as $post_field ){
        if( isset( $_POST[ $post_field ] ) ){
          $post_info[ $post_field ] = $_POST[ $post_field ];
        }
      }

      $post_id = wp_insert_post( $post_info );

      $custom_fields = array();


      foreach( $_POST as $slug => $value ){
        if( strpos( $slug, 'tax_') !== false ){

          $slug = str_replace( "tax_", "", $slug );

          // array_push( $taxonomies, $slug );
          wp_set_post_terms( $post_id, $value, $slug );
          // echo $slug;
        }
        elseif( strpos( $slug, 'cf_') !== false ){
          $slug = str_replace( "cf_", "", $slug );

          update_post_meta( $post_id, $slug, $value );
        }

      }

    } //end of if statement

  }


  function display_section( $section, $args ){

    $args = wp_parse_args( $args, array(
      'prev_text'		=> "Previous",
      'next_text'		=> "Next",
      'submit_text'	=> "Submit",
      'totalSlides'	=> 1,
      'i'						=> 0

    ) );

    echo "<section class='meteor-slide'>";
    $this->display_inline_section( $section );


    _e( "<ul class='meteor-list meteor-list-inline'>" );

    // HIDE IN THE FIRST PAGE OF THE FORM
    if( $args['i'] ){ _e( "<li><button data-behaviour='meteor-slide-prev'>" . $args['prev_text'] . "</button></li>" ); }

    // IN THE LAST FORM, THE TEXT SHOULD CHANGE TO SUBMIT
    if( $args['i'] != $args['totalSlides'] - 1 ){
      _e( "<li><button data-behaviour='meteor-slide-next'>" . $args['next_text'] . "</button></li>" );
    }
    else{
      _e( "<li><button type='submit'>" . $args['submit_text'] ."</button></li>" );
    }

    _e( "</ul>" );

    echo "</section>";
  }


  function display_inline_section( $section ){
    $section['class'] = isset( $section['class'] ) ? $section['class'] : "";
    $section['class'] .= " inline-section";

    echo "<div class='" . $section['class'] . "'>";
    if( isset( $section['page_title'] ) ){
      _e( "<h3>".$section['page_title']."</h3>" );
    }

    echo "<div class='section-fields'>";
    foreach( $section['fields'] as $field ){
      $this->display_field( $field );
    }
    echo "</div></div>";
  }

  function display_field( $field ){

    $options = array();

    switch( $field['type'] ){
      case 'nested-fields':
        $this->display_inline_section( $field );
        break;

      case 'cf':
        // ITERATE THE USER DEFINED OPTIONS INTO THE COMPATIBLE FORM OF OPTIONS
        if( isset( $field['options'] ) && is_array( $field['options'] ) && count( $field['options'] ) ){
          foreach( $field['options'] as $option ){
            array_push( $options, array( 'slug' => $option, 'name' => $option['value'] ) );
          }
        }

        // UPDATE TYPEVAL FOR CUSTOM FIELDS WITH THE POST META NAME
        $field['typeval'] = $field['name'];

        break;

      case 'post':

        switch( $field['typeval'] ){
          case 'content':
            $field['form'] = 'textarea';
            break;

          case 'date':
            $field['form'] = 'date';
            break;

          default:
            $field['form'] = 'text';

        }



        break;

      case 'tax':
        // GET ALL THE TAXONOMY TERMS
        $tax_terms = get_terms( array(
          'taxonomy'    => $field['typeval'],
          'hide_empty'  => false
        ) );

        // ITERATE AND ADD TO OPTIONS ARRAY
        foreach( $tax_terms as $term ){

          // print_r( $term );
          array_push( $options, array( 'slug' => $term->term_id, 'name' => $term->name, ) );
        }
        break;
      default:

    }

    // NAME ATTRIBUTE FOR THE INPUT FIELD - this clearly identifies if the field is postfield, taxonomy or custom field
    $field['name'] = $field['type'].'_'.$field['typeval'];

    // SETTING FIELD CLASS
    $field['class'] = isset( $field['class'] ) ? $field['class']." form-field" : "form-field";

    echo "<div class='".$field['class']."'>";
    echo "<label>".$field['label']."</label>";
    include( "templates/" . $field['form'] . ".php" );
    echo "</div>";

  }


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
