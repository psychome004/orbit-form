jQuery.fn.repeater_options = function( parent_name ){

  return this.each(function() {
    var $el = jQuery( this );
      data  = $el.data( 'atts' );

    var repeater = ORBIT_REPEATER( {
      $el				      : $el,
      btn_text		    : '+ Add Option',
      list_id         : 'orbit-options-repeater-list',
      list_item_id	  : 'orbit-options-repeater',
      init	: function( repeater ){

			// ITERATE THROUGH EACH PAGES IN THE DB
			jQuery.each( data, function( i, option ){

				if( option['value'] != undefined ){
					repeater.addItem( option );
				}
			});
		},
		addItem	: function( repeater, $list_item, $closeButton, option ){

			/*
			* ADD LIST ITEM TO THE UNLISTED LIST
			* TEXTAREA: page TITLE
			* HIDDEN: page ID
			* HIDDEN: page COUNT
			*/

			if( option == undefined || option['value'] == undefined ){
				option = { value : '' };
			}

			// CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
			//repeater.addCollapsibleItem( $list_item, $closeButton );

      var common_name = parent_name + '[' + repeater.count + ']';

			var $textarea = repeater.createField({
				element	: 'textarea',
				attr	: {
					'data-behaviour': 'space-autoresize',
					'placeholder'	 : 'Type Options Title Here',
					'name'			   : common_name + '[value]',
					'value'			   : 'Option ' + ( repeater.count + 1 )
				},
				append	: $list_item
			});

      console.log( option );

			if( option['value'] ){ $textarea.val( option['value'] ); }

      // CREATE HIDDEN FIELD THAT WILL HOLD THE PAGE RANK
			var $hiddenRank = repeater.createField({
				element	: 'input',
				attr	: {
					'type'				    : 'hidden',
					'value'				    : repeater.count,
					'data-behaviour' 	: 'orbit-option-rank',
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
			repeater.$list.find( '[data-behaviour~=orbit-option-rank]' ).each( function(){
				var $hiddenRank = jQuery( this );
				$hiddenRank.val( rank );
				rank++;
			});
		},



    } );//orbit-repeater

  });
};
