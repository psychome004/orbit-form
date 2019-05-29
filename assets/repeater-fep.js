jQuery.fn.repeater_fep = function( atts ){

  return this.each(function() {

    var $el = jQuery( this ),
    atts  = $el.data( 'atts' );
    console.log( atts );

    var repeater = ORBIT_REPEATER( {
			$el				      : $el,
			btn_text		    : '+ Add Form Slide',
			close_btn_text	: 'Delete Form Field',
      list_id         : 'orbit-slide-repeater-list',
      list_item_id	  : 'orbit-slide-repeater',
			init	: function( repeater ){

        // ITERATE THROUGH EACH PAGES IN THE DB
        jQuery.each( atts.db, function( i, filter ){

          if( filter['label']){
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

				// CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
				repeater.addCollapsibleItem( $list_item, $closeButton );

				var $header = $list_item.find( '.list-header' );
				var $content = $list_item.find( '.list-content' );

				// LABEL
				var $textarea = repeater.createField({
					element	: 'textarea',
					attr	: {
						'data-behaviour': 'space-autoresize',
						'placeholder'	: 'Type Form Slide Name Here',
						'name'			: 'form_slide[' + repeater.count + '][page]',
						'value'			: 'Form Field ' + ( repeater.count + 1 )
					},
					append	: $header
				});
				//$textarea.space_autoresize();
				if( filter['label'] ){ $textarea.val( filter['label'] ); }



        var $filter_type = repeater.createDropdownField({
          attr	:  {
          'name'			: 'orbit_filter[' + repeater.count + '][type]'
          },
          value   : filter['type'] ? filter['type'] : '',
          options : atts['types'],
          append	: $content,
          label	  : 'Choose Type'
        });

        //Filter typeVAL
        var $filter_typeval = repeater.createDropdownField({
          attr	: {
            'name'			: 'orbit_filter[' + repeater.count + '][typeval]'
          },
          options : {},
          append	: $content,
          label   : 'Choose Type Field'
        });

        //Filter form style
        var $form_field = repeater.createDropdownField({
          attr    : {
            'name'			: 'orbit_filter[' + repeater.count + '][form]',
          },
          value   : filter['form'] ? filter['form'] : '',
          options : atts['forms'],
          append	: $content,
          label   : 'Choose Form Field'
        });
        //if( filter['form'] ){ $form_field.selectOption( filter['form'] ); }


        // OPTIONS OF FILTER TYPE BY VALUE ARE RESET BASED ON THE VALUE SELECTED IN FILTER TYPE
        function updateOptionsForFilterTypeValue(){
          var type = $filter_type.find('select').val(),
            options = atts[ type + '_types' ];

            if( options == undefined ){
              $filter_typeval.hide();
            }
            else{
              $filter_typeval.setOptions( options );
              $filter_typeval.show();
            }

        }

        // ON CHANGE OF FILTER TYPE TRIGGER AN UPDATE IN OPTIONS OF FILTER TYPE BY VALUE
        $filter_type.find('select').change(function(){
          updateOptionsForFilterTypeValue();
        });
        updateOptionsForFilterTypeValue();  // SET OPTIONS FOR THE FIRST LOAD

        // DEFAULT VALUE COMING FROM THE DB
        if( filter['typeval'] ){ $filter_typeval.selectOption( filter['typeval'] ); }

        //CREATE A HIDDEN FIELD
        var hidden = repeater.createField({
          element	: 'input',
          attr	: {
            'type'	          : 'hidden',
            'value'				    : repeater.count,
            'data-behaviour' 	: 'orbit-form-slide',
            'name'				    : 'form_slide[' + repeater.count + '][order]'
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
