jQuery.fn.repeater_fields = function( parent_name, atts ){

  return this.each(function() {

    var $el  = jQuery( this ),
        data = $el.data( 'atts' );


    var repeater = ORBIT_REPEATER( {
			$el				      : $el,
			btn_text		    : '+ Add Form Field',
			close_btn_text	: 'Delete Form Field',
      list_id         : 'orbit-slide-repeater-list',
      list_item_id	  : 'orbit-slide-repeater',
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

        // console.log(filter);

				if( filter == undefined ){
					filter = { label : '' };
				}

        var common_name = parent_name + '[' + repeater.count + ']';

				// CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
				repeater.addCollapsibleItem( $list_item, $closeButton );

				var $header = $list_item.find( '.list-header' );
				var $content = $list_item.find( '.list-content' );

				// TEXTAREA FOR FORM FIELD LABEL
				var $textarea = repeater.createField({
					element	: 'textarea',
					attr	: {
						'data-behaviour': 'space-autoresize',
						'placeholder'	  : 'Type Form Field Name Here',
						'name'			    : common_name + '[label]',
						'value'			    : 'Form Field ' + ( repeater.count + 1 )
					},
					append	: $header
				});
				//$textarea.space_autoresize();
				if( filter['label'] ){ $textarea.val( filter['label'] ); }

        var $filter_type = repeater.createDropdownField({
          attr	:  {
          'name'			: common_name + '[type]'
          },
          options : atts['types'],
          append	: $content,
          label	  : 'Choose Type'
        });
        if( filter['type'] != undefined ){ $filter_type.selectOption( filter['type'] ); }

        // FILTER TYPEVAL
        var $filter_typeval = repeater.createDropdownField({
          attr	: {
            'name'	: common_name + '[typeval]'
          },
          options : {},
          append	: $content,
          label   : 'Choose Type Field'
        });

        // console.log( filter );


        //Filter form style
        var $form_field = repeater.createDropdownField({
          attr    : {
            'name'			: common_name + '[form]',
          },
          value   : filter['form'] ? filter['form'] : '',
          options : atts['forms'],
          append	: $content,
          label   : 'Choose Form Field'
        });

        // //POST STATUS
        // var $filter_status = repeater.createDropdownField({
        //   attr    : {
        //     'name'			: common_name + '[post_status]',
        //   },
        //   value   : filter['post_status'] ? filter['post_status'] : '',
        //   options : atts['post_status'],
        //   append	: $content,
        //   label   : 'Choose Status'
        // });

        // FIELD NAME - ONLY FOR CUSTOM FIELDS
        var $field_div = repeater.createField({
          element	: 'div',
          attr	  : {
            class: 'field-name'
          },
          append	: $content
        });

        var $field_label = repeater.createField({
          element	: 'label',
          attr	  : {},
          html    : 'Field Name',
          append	: $field_div
        });

        var $field_name = repeater.createField({
          element	: 'input',
          attr	: {
            'type'        : 'text',
            'placeholder'	: 'Type Name of the Field',
            'class'       :  'name-attr',
            'name'        : common_name + '[name]'
          },
          append	: $field_div
        });
        if( filter['name'] != undefined ){ $field_name.val( filter['name'] ); }

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


        // OPTIONS OF FILTER TYPE BY VALUE ARE RESET BASED ON THE VALUE SELECTED IN FILTER TYPE
        function updateOptionsForFilterTypeValue(){
          var type = $filter_type.find('select').val(),
            options = atts[ type + '_types' ];

            //HIDES FORM FIELD DROPDOWN WHEN THE TYPE IS POST
            if( type=='post' ){ $form_field.hide(); }
            else{ $form_field.show(); }

            //HIDES TYPE FIELD DROPDOWN WHEN THE TYPE IS CUSTOM FIELD
            if( options == undefined ){ $filter_typeval.hide(); }
            else{
              $filter_typeval.setOptions( options );
              $filter_typeval.show();
            }
        }

        // SHOW OR HIDE OPTIONS FOR CUSTOM FIELDS ONLY WHEN THE MULTIPE FORM FIELDS ARE SELECTED
        function showOrHideOptions(){
          var type              = $filter_type.find('select').val(),
            multiple_formfields = ['checkbox', 'dropdown', 'bt_dropdown_checkboxes', 'radio'],
            formfield           = $form_field.find('select').val();

          if( ( jQuery.inArray( formfield, multiple_formfields ) != -1 ) && ( type == 'cf' ) ){ $fep_options.show(); }
          else{ $fep_options.hide(); }

        }

        function showOrHideNameField(){
          var type = $filter_type.find('select').val();
          if( type=='cf' ){ $field_div.show(); }
          else{ $field_div.hide(); }
        }

        $list_item.on( 'change', function(){
          showOrHideOptions();
          showOrHideNameField();
        });
        $filter_type.on( 'change', function(){
          updateOptionsForFilterTypeValue();
        });
        showOrHideOptions();
        showOrHideNameField();

        updateOptionsForFilterTypeValue();
        // DEFAULT VALUE COMING FROM THE DB
        if( filter['typeval'] ){ $filter_typeval.selectOption( filter['typeval'] ); }


        //CREATE A HIDDEN FIELD
        var hidden = repeater.createField({
          element	: 'input',
          attr	: {
            'type'	          : 'hidden',
            'value'				    : repeater.count,
            'data-behaviour' 	: 'orbit-form-slide',
            'name'				    : common_name + '[order]'
          },
          append	: $list_item
        });



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
				var rank = 0;
				repeater.$list.find( '[data-behaviour~=orbit-form-slide]' ).each( function(){
					var $hiddenRank = jQuery( this );
					$hiddenRank.val( rank );
					rank++;
				});
			},
		} );//orbit-repeater

  });
};
