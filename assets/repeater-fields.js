jQuery.fn.repeater_fields = function( parent_name, atts ){

  return this.each(function() {

    var $el  = jQuery( this ),
        data = $el.data( 'atts' );


    var repeater = ORBIT_REPEATER( {
			$el				      : $el,
			btn_text		    : '+ Add Field',
			close_btn_text	: 'Delete Form Field',
      list_id         : 'orbit-slide-repeater-list',
      list_item_id	  : 'orbit-slide-repeater',
      list_item_types : atts['types'],
			init	: function( repeater ){

        // ITERATE THROUGH EACH PAGES IN THE DB
        jQuery.each( data, function( i, filter ){

          if( filter != undefined && filter['label'] != undefined ){

            repeater.addItem( filter );

          }
        });

			},
			addItem	: function( repeater, $list_item, $closeButton, filter ){

				/*
				* ADD LIST ITEM TO THE UNLISTED LIST
				* TEXTAREA: page TITLE
				* HIDDEN: page ID
				* HIDDEN: page COUNT
				*/

        if( filter == undefined || filter['type'] == undefined ){
					filter = { type : 'tax' };
				}

        // CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
				repeater.addCollapsibleItem( $list_item, $closeButton );

				var $header           = $list_item.find( '.list-header' ),
				    $content          = $list_item.find( '.list-content' ),
            filter_type_text  = atts['types'][ filter['type'] ],
            common_name       = parent_name + '[' + repeater.count + ']';

				// TEXTAREA FOR FORM FIELD LABEL
				var $textarea = repeater.createField({
					element	: 'textarea',
					attr	: {
						'data-behaviour': 'space-autoresize',
						'placeholder'	  : 'Type Form Field Name Here',
						'name'			    : common_name + '[label]',
						'value'			    : filter_type_text + ' ' + ( repeater.count + 1 )
					},
					append	: $header
				});
				//$textarea.space_autoresize();
				if( filter['label'] ){ $textarea.val( filter['label'] ); }

        // BUBBLE FIELD THAT IDENTIFIES THE REPEATER FIELD ITEM
        var $bubble = repeater.createField({
          element : 'div',
          attr    : {
            class : 'orbit-bubble'
          },
          html    : filter_type_text,
          append  : $header
        });

        // COMMON FIELD - TYPE, CAN BE REPLACED WITH HIDDEN TEXT FIELD
        var $type = createDropdownField( {
          slug    : 'type',
          options : atts['types'],
          append	: $content,
          label   : 'Choose Type Field'
        } );
        $type.hide();

        // CREATE SUB FIELDS IN THE FORM BY CHECKING IF THE TYPE IS A SECTION OR NOT
        if( filter['type'] == 'section' ){

          // CSS CLASS
          var $css_class = createTextField( {
            label       : 'CSS Class',
            slug        : 'class',
            placeholder : "Type css class for this section",
            help        : 'Custom CSS class to uniqely identify this section',
            append      : $content
          } );

          // HTML - VISUAL EDITOR
          var $html = repeater.createRichText({
            attr:{
              name  : common_name + '[html]',
              id    : 'sections_html_' + Math.floor((Math.random() * 10) + 1) + '_' + repeater.count
            },
            html   : filter['html'] ? filter['html'] : '',
            append : $content
          });

          // NESTED FIELDS
          var $nested_fields_container = repeater.createField({
            element	: 'div',
            attr	: {
              'class'           : 'orbit-nested-fields',
              'data-behaviour' 	: 'orbit-fields-repeater',
              'data-atts'       : JSON.stringify( filter['fields'] ? filter['fields'] : [] )
            },
            append	: $content
          });
          $nested_fields_container.repeater_fields( common_name + '[fields]', atts );
        }
        else{

          // REQUIRED FIELD
          var required_flag = false;
  				if( filter && filter['required'] && filter['required'] > 0 ){ required_flag = true; }
          var $required = repeater.createBooleanField({
            attr   :  {
              name		: common_name + '[required]',
              checked : required_flag
            },
            label  :  'Required',
            append :  $content
          });

          // FILTER TYPEVAL
          var $typeval = createDropdownField( {
            slug    : 'typeval',
            options : {},
            append	: $content,
            label   : 'Choose Type Field'
          } );

          // FORM FIELD TYPE
          var $form_field = createDropdownField( {
            slug    : 'form',
            options : atts['forms'],
            append	: $content,
            label   : 'Choose Form Field'
          } );

          // PLACEHOLDER FOR INPUT FIELDS / TEXTAREAS
          var $placeholder = createTextField( {
            label       : 'Placeholder',
            slug        : 'placeholder',
            placeholder : "Type something here",
            help        : 'This option is only available for textarea and input text boxes',
            append      : $content
          } );

          // CUSTOM META NAME - ONLY FOR CUSTOM FIELDS
          var $meta_name = createTextField( {
            label       : 'Metafield Name',
            slug        : 'name',
            placeholder : "Type name of the metafield",
            help        : 'Only enter slugs as field names. For example: <b>contact-name</b>',
            append      : $content
          } );

          // Container for holding the custom field's checkboxes
          var $fep_options = repeater.createField({
            element	: 'div',
            attr	: {
              'data-behaviour' 	: 'orbit-fep-options-repeater',
              'data-atts'       : JSON.stringify( filter['options'] ? filter['options'] : [] )
            },
            append	: $content
          });
          $fep_options.repeater_options( common_name + '[options]' );

          // OPTIONS LABEL
          var $options_list = repeater.createField({
            element	: 'label',
            attr	  : {
              class: 'options-label'
            },
            html    : 'Options List',
            prepend	: $fep_options
          });
        }

        // REUSABLE HELPER FUNCTION TO CREATE INPUT TEXT FIELD
        function createTextField( field ){
          var $field = repeater.createInputTextField({
            label : field['label'] ? field['label'] : '',
            attr  : {
              placeholder : field['placeholder'] ? field['placeholder'] : '',
              name        : common_name + '[' + field['slug'] +']'
            },
            help    : field['help'] ? field['help'] : '',
            append  : field['append']
          });
          if( filter[ field['slug'] ] != undefined ){ $field.val( filter[ field['slug'] ] ); }
          return $field;
        }

        // REUSABLE HELPER FUNCTION TO CREATE DROPDOWN FIELD
        function createDropdownField( field ){
          var $field = repeater.createDropdownField({
            attr	: {
              name	: common_name + '[' + field['slug'] + ']'
            },
            value   : filter[ field['slug'] ] ? filter[ field['slug'] ] : '',
            options : field['options'],
            append	: field['append'],
            label   : field['label']
          });
          if( filter[ field['slug'] ] != undefined ){ $field.selectOption( filter[ field['slug'] ] ); }
          return $field;
        }


        // OPTIONS OF FILTER TYPE BY VALUE ARE RESET BASED ON THE VALUE SELECTED IN FILTER TYPE
        function updateOptionsForFilterTypeValue(){
          if( filter['type'] != 'section' ){
            var type = $type.find('select').val(),
              options = atts[ type + '_types' ];

            //HIDES FORM FIELD DROPDOWN WHEN THE TYPE IS POST
            if( type == 'post' || type == 'section' ){ $form_field.hide(); }
            else{ $form_field.show(); }

            //HIDES TYPE FIELD DROPDOWN WHEN THE TYPE IS CUSTOM FIELD
            if( options == undefined ){ $typeval.hide(); }
            else{
              $typeval.setOptions( options );
              $typeval.show();
            }
          }
        }

        // SHOW OR HIDE OPTIONS FOR CUSTOM FIELDS ONLY WHEN THE MULTIPE FORM FIELDS ARE SELECTED
        function showOrHideFields(){

          if( filter['type'] != 'section' ){
            var type              = $type.find('select').val(),
              multiple_formfields = ['checkbox', 'dropdown', 'bt_dropdown_checkboxes', 'radio'],
              input_fields        = ['text', 'multiple-text', 'textarea'],
              formfield           = $form_field.find('select').val();

            // SHOW OR HIDE OPTIONS
            if( ( jQuery.inArray( formfield, multiple_formfields ) != -1 ) && ( type == 'cf' ) ){ $fep_options.show(); }
            else{ $fep_options.hide(); }

            // SHOW OR HIDE PLACEHOLDER
            if( ( jQuery.inArray( formfield, input_fields ) != -1 ) && ( type == 'cf' ) ){ $placeholder.show(); }
            else{ $placeholder.hide(); }

            // SHOW OR HIDE META NAME FIELD
            if( type == 'cf' ){ $meta_name.show(); }
            else{ $meta_name.hide(); }
          }
        }

        // CHECK WHENEVER THE FORM IS CHANGED
        $list_item.on( 'change', function(){ showOrHideFields(); });
        showOrHideFields();

        // UPDATE ONLY WHEN THE TYPE IS CHANGED
        updateOptionsForFilterTypeValue();

        // DEFAULT VALUE COMING FROM THE DB
        if( filter['typeval'] ){ $typeval.selectOption( filter['typeval'] ); }

        $closeButton.click( function( ev ){
					ev.preventDefault();
					if( confirm( 'Are you sure you want to remove this?' ) ){
						// IF PAGE ID IS NOT EMPTY THAT MEANS IT IS ALREADY IN THE DB, SO THE ID HAS TO BE PUSHED INTO THE HIDDEN DELETED FIELD
						$list_item.remove();
					}
				});

      },
			reorder: function( repeater ){
				/*
				* REORDER LIST
				*/
        /*
				var rank = 0;
				repeater.$list.find( '[data-behaviour~=orbit-form-slide]' ).each( function(){
					var $hiddenRank = jQuery( this );
					$hiddenRank.val( rank );
					rank++;
				});
        */
			},
		} );//orbit-repeater

  });
};
