jQuery.fn.repeater_pages = function(){

  return this.each(function() {
    var $el = jQuery( this );
      atts  = $el.data( 'atts' );

    var repeater = ORBIT_REPEATER( {
      $el				      : $el,
      btn_text		    : '+ Add Page',
      close_btn_text	: 'Delete Page',
      list_id         : 'orbit-page-repeater-list',
      list_item_id	  : 'orbit-page-repeater',
      init	: function( repeater ){

			// ITERATE THROUGH EACH PAGES IN THE DB
			jQuery.each( atts['db'], function( i, page ){

				if( page['page_title'] != undefined ){
					repeater.addItem( page );
				}
			});
		},
		addItem	: function( repeater, $list_item, $closeButton, page ){

			/*
			* ADD LIST ITEM TO THE UNLISTED LIST
			* TEXTAREA: page TITLE
			* HIDDEN: page ID
			* HIDDEN: page COUNT
			*/

			if( page == undefined || page['page_title'] == undefined ){
				page = { page_title : '' };
			}

			// CREATE COLLAPSIBLE ITEM - HEADER AND CONTENT
			repeater.addCollapsibleItem( $list_item, $closeButton );

			var $header = $list_item.find( '.list-header' );
			var $content = $list_item.find( '.list-content' );

			// PAGE TITLE
			var $textarea = repeater.createField({
				element	: 'textarea',
				attr	: {
					'data-behaviour': 'space-autoresize',
					'placeholder'	 : 'Type Page Title Here',
					'name'			   : 'fep[' + repeater.count + '][page_title]',
					'value'			   : 'Page ' + ( repeater.count + 1 )
				},
				append	: $header
			});

			if( page['page_title'] ){ $textarea.val( page['page_title'] ); }

      var $fep_form_container = repeater.createField({
				element	: 'div',
				attr	: {
					'data-behaviour' 	: 'orbit-fields-repeater',
          'data-atts'       : JSON.stringify( page['fields'] ? page['fields'] : [] )

				},
				append	: $content
			});
      $fep_form_container.repeater_fields( 'fep[' + repeater.count + '][fields]', atts );

      // CREATE HIDDEN FIELD THAT WILL HOLD THE PAGE RANK
			var $hiddenRank = repeater.createField({
				element	: 'input',
				attr	: {
					'type'				    : 'hidden',
					'value'				    : page['rank'] ? page['rank'] : 0,
					'data-behaviour' 	: 'orbit-page-rank',
					'name'				    : 'fep[' + repeater.count + '][rank]'
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
			repeater.$list.find( '[data-behaviour~=orbit-page-rank]' ).each( function(){
				var $hiddenRank = jQuery( this );
				$hiddenRank.val( rank );
				rank++;
			});
		},



    } );//orbit-repeater

  });
};


jQuery(document).ready(function(){
  jQuery('[data-behaviour="orbit-fep-pages"]').repeater_pages();
  /*

  jQuery( '[data-behaviour="orbit-fep-pages"] .button' ).click(function(){
     jQuery('[data-behaviour="orbit-fep-repeater"]').repeater_fep().appendTo( jQuery('[data-behaviour="form-container"]') );
    // alert( 'success' );
  });
  */
});
