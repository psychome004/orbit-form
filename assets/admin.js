jQuery(document).ready(function(){
  jQuery('[data-behaviour="orbit-form-pages"]').each(function(){

    var $el = jQuery( this ),
      atts  = $el.data( 'atts' );

    console.log( atts );

    var repeater = ORBIT_REPEATER( {
			$el				      : $el,
			btn_text		    : '+ Add Page',
			close_btn_text	: 'Delete Page',
      list_id         : 'orbit-page-repeater-list',
      list_item_id	  : 'orbit-page-repeater',
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

        console.log(filter);

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
						'placeholder'	: 'Type Label Here',
						'name'			: 'orbit_page[' + repeater.count + '][page]',
						'value'			: 'Page ' + ( repeater.count + 1 )
					},
					append	: $header
				});
				//$textarea.space_autoresize();
				if( filter['label'] ){ $textarea.val( filter['label'] ); }

        //ORBIT FILTER FIELDS
        var hide_label_flag = false;
				if( filter && filter['hide_label'] && filter['hide_label'] > 0 ){
					hide_label_flag = true;
				}
        var hide_label = repeater.createBooleanField({
          attr   :  {
            name		: 'orbit_filter[' + repeater.count + '][hide_label]',
            checked : hide_label_flag
          },
          label  :  'Hide Label',
          append :  $content
        });

        //Filter form style
        var $form_field = repeater.createDropdownField({
          attr    : {
            'name'			: 'orbit_filter[' + repeater.count + '][form]',
          },
          value   : filter['form'] ? filter['form'] : '',
          options : atts['form'],
          append	: $content,
          label   : 'Form Field'
        });
        //if( filter['form'] ){ $form_field.selectOption( filter['form'] ); }

        var $filter_type = repeater.createDropdownField({
					attr	:  {
					'name'			: 'orbit_filter[' + repeater.count + '][type]'
					},
          value   : filter['type'] ? filter['type'] : '',
          options : atts['types'],
					append	: $content,
					label	  : 'Filter by'
				});

        //Filter typeVAL
        var $filter_typeval = repeater.createDropdownField({
          attr	: {
            'name'			: 'orbit_filter[' + repeater.count + '][typeval]'
          },
          options : {},
          append	: $content,
          label   : 'Filter Value'
        });

        //ORBIT FILTER FIELDS
        var tax_hide_empty_flag = false;
				if( filter && filter['tax_show_empty'] && filter['tax_show_empty'] > 0 ){
					tax_hide_empty_flag = true;
				}

        var $tax_hide_empty = repeater.createBooleanField({
          attr   :  {
            name		: 'orbit_filter[' + repeater.count + '][tax_show_empty]',
            checked	: tax_hide_empty_flag,
          },
          label  :  'Show empty terms',
          append :  $content
        });



        // OPTIONS OF FILTER TYPE BY VALUE ARE RESET BASED ON THE VALUE SELECTED IN FILTER TYPE
        function updateOptionsForFilterTypeValue(){
          var type = $filter_type.find('select').val(),
            options = atts[ type + '_options' ];
          $filter_typeval.setOptions( options );

          if( type=='tax' ){
            $tax_hide_empty.show();
          }
          else{
            $tax_hide_empty.hide();
          }
        }

        // ON CHANGE OF FILTER TYPE TRIGGER AN UPDATE IN OPTIONS OF FILTER TYPE BY VALUE
        $filter_type.find('select').change(function(){
          updateOptionsForFilterTypeValue();
          // hide_empty.show();
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
            'data-behaviour' 	: 'orbit-rank',
            'name'				    : 'orbit_filter[' + repeater.count + '][order]'
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
				repeater.$list.find( '[data-behaviour~=orbit-rank]' ).each( function(){
					var $hiddenRank = jQuery( this );
					$hiddenRank.val( rank );
					rank++;
				});
			},
		} );


  });
});
