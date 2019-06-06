<?php

// HANDLES POST SUBMISSION
$form_success_flag = $this->save();

$lang = $atts['lang'];

/* REPORT TYPES, VICTIMS AND LOCATIONS FROM THE DB */
$report_types = $this->getOptionsFromTaxonomy( 'report-type', $lang );
$victims      = $this->getOptionsFromTaxonomy( 'victims', $lang );
$locations    = $this->getOptionsFromTaxonomy( 'locations', $lang );

// SEPERATE STATE AND DISTRICTS FROM LOCATIONS
$states = array();
$districts = array();
foreach ( $locations as $location ) {
  if( $location['parent'] ){ array_push( $districts, $location ); }
  else{ array_push( $states, $location ); }
}
/* REPORT TYPES, VICTIMS AND LOCATIONS FROM THE DB */

// GET FORM LABELS
$labels = $this->getLabels();

$form_sections = array(
  'report'  => array(
    'fields'    => array(

      /* REPORT DATE */
	    'date'  => array(
        'type'        => 'input',
        'input_type'  => 'date',
        'label'       => $labels['report-date'][ $lang ],
        'name'        => 'incident-date',
        'class'       => 'form-required'
      ),
      /* REPORT DATE */

      /* REPORT ADDRESS INFORMATION */
      'address-form'  => array(
        'title'	      => $labels['address-form'][ $lang ],
        'desc'        => $labels['address-form-desc'][ $lang ],
        'type'        => 'nested-fields',
        'class'       => 'form-address box',
        'fields'      => array(
          'state' => array(
            'type'        => 'dropdown',
            'options'     => $states,
            'label'       => $labels['state-title'][ $lang ],
            'name'        => 'state',
            'class'       => 'form-required form-state',
            'placeholder' => $labels['state-placeholder'][ $lang ],
          ),
          'district'  => array(
            'type'        => 'dropdown',
            'options'     => $districts,
            'label'       => $labels[ 'district-title' ][ $lang ],
            'name'        => 'district',
            'class'       => 'form-required form-district',
            'placeholder' => $labels[ 'district-placeholder' ][ $lang ],

          ),
          'address' => array(
            'type'        => 'input',
            'input_type'  => 'text',
            'label'       => $labels[ 'address-title' ][ $lang ],
            'name'        => 'incident-address',
            'class'       => 'form-address-text',
            'placeholder' => $labels[ 'optional' ][ $lang ]
          ),
        ),
      ),
      /* REPORT ADDRESS INFORMATION */

      /* CONTACT INFORMATION */
      'contact-info'  => array(
        'class'		=> 'box',
    	  'title'		=> $labels[ 'contact-form-title' ][ $lang ],
    	  'desc'		=> $labels[ 'contact-form-desc' ][ $lang ],
        'type'    => 'nested-fields',
        'fields'  => array(
            'name'  => array(
            'type'        => 'input',
            'input_type'  => 'text',
            'label'       => $labels[ 'contact-name-title' ][ $lang ],
            'name'        => 'contact-name',
            'placeholder' => $labels[ 'optional' ][ $lang ]
          ),
          'contact-type'  => array(
            'class'   => 'form-required',
            'type'    => 'checkbox',
            'label'   => $labels[ 'contact-type-title' ][ $lang ],
            'name'    => 'contact-type',
            'options' => array(
              array( 'slug' => 'contact-phone', 'title' => $labels[ 'option-phone' ][ $lang ] ),
              array( 'slug' => 'contact-email', 'title' => $labels[ 'option-email' ][ $lang ] )
            )
          ),
          'phone'  => array(
            'type'        => 'input',
            'input_type'  => 'number',
            'label'       => $labels[ 'contact-phone-title' ][ $lang ],
            'name'        => 'contact-phone',
            'class'       => 'form-required'
          ),
          'email'  => array(
            'type'        => 'input',
            'input_type'  => 'email',
            'label'       => $labels[ 'contact-email-title' ][ $lang ],
            'name'        => 'contact-email',
            'class'       => 'form-required'
          ),
        ),
      ),
      /* CONTACT INFORMATION */
    )
  ),

  'categories' => array(
    'class' 	=> '',
    'fields' 	=> array(
      'report-type'  => array(
		    'class'   => 'form-categories',
        'type'    => 'checkbox',
        'label'   => $labels[ 'report-type-title' ][ $lang ],
        'options' => $report_types,
        'name'    => 'report-type[]'
      ),
      'victims'  => array(
        'class'   => 'form-categories',
		    'type'    => 'checkbox',
        'label'   => $labels[ 'victims-title' ][ $lang ],
        'options' => $victims,
        'name'    => 'victims[]'
      ),
    ),
  ),


  'extra' => array(
    'fields'  => array(
      'description'  => array(
        'type'        => 'textarea',
        'label'       => $labels[ 'report-desc' ][ $lang ],
		    'placeholder'	=> $labels[ 'optional' ][ $lang ],
        'name'        => 'description'
      ),
      'multiple-links'  => array(
        'type'        => 'nested-fields',
        'title'	  => $labels[ 'extra-form-title' ][ $lang ],
        'desc'    => $labels[ 'extra-form-desc' ][ $lang ],
        'class'	  => 'box',
        'fields'      => array(
          'images'  => array(
    		    'class'       => 'form-images form-multi-fields',
            'type'        => 'multiple-fields',
            'fields_type' => 'multiple-image',
            'label'       => $labels[ 'images-title' ][ $lang ],
            'name'        => 'files',
            'btn_text'    => $labels[ 'add-another' ][ $lang ]
          ),
          'links'  => array(
    		    'class'       => 'form-links form-multi-fields',
            'type'        => 'multiple-fields',
            'fields_type' => 'multiple-text',
            'label'       => $labels[ 'links-title' ][ $lang ],
            'name'        => 'links[]',
            'btn_text'    => $labels[ 'add-another' ][ $lang ]
          ),
        )

      ),

      // 'recaptcha'  => array(
      //   'type'      => 'recaptcha',
      //   'label'     => 'Google Recaptcha',
      //   'class'     => 'hide-label'
      // )
    ),
  ),
);

$error_messages = array(
  // 'captcha'         => $labels[ 'error-captcha' ][ $lang ],
  'missed'          => $labels[ 'error-missed' ][ $lang ],
  'contact-number'  => $labels[ 'error-contact-number' ][ $lang ]
);

// echo '<script src="https://www.google.com/recaptcha/api.js?hl='.$lang.'"></script>';

echo "<form data-error='".wp_json_encode( $error_messages )."' class='soah-fep' data-behaviour='meteor-slides' id='featured_upload' method='post' enctype='multipart/form-data'>";
if( !$_POST ){
  // BASIC FORM INFORMATION

  $this->display_field( array(
    'class'   => 'lang-switcher',
    'type'    => 'dropdown',
    'options' => array(
      array(
        'slug'  => 'en',
        'title' => 'English'
      ),
      array(
        'slug'  => 'hi',
        'title' => 'Hindi'
      )
    ),
    'selected'  => $lang
  ) );



  echo "<h3>" . $labels[ 'report-form' ][ $lang ] . "</h3>";
  echo "<p class='section-desc'>" . $labels[ 'report-form-desc' ][ $lang ] . "</p>";



  echo "<div class='form-progress'></div>";

  wp_nonce_field( 'soah-fep' );

  $i = 0;
  foreach ($form_sections as $section) {
    $this->display_section( $section, array(
      'i'           => $i,
      'totalSlides' => count( $form_sections ),
      'prev_text'		=>  $labels[ 'previous' ][ $lang ],
			'next_text'		=>  $labels[ 'next' ][ $lang ],
			'submit_text'	=>  $labels[ 'submit' ][ $lang ],
    ) );
    $i++;
  }



  echo "<div class='form-alert error'></div>";
  //echo "<input class='submit' name='submit' type='submit' value='Submit Report' />";

}
else{
  if( $form_success_flag ){ $message = "Report has been submitted successfully"; }
  else{ $message = "Report could not be submitted. The required fields were missing. Please try again."; }

  // DISPLAY MESSAGE ON FORM SUBMISSION
  echo "<div style='margin-top:50px;' class='form-alert'>" . $message . "</div>";

  // REDIRECT AFTER A DELAY
  if( $form_success_flag ){
    echo "<script>function refreshPage(){ window.location.href = '" . $atts['redirect_to'] . "';} setTimeout( refreshPage, 500 );</script>";
  }
}

echo "</form>";
