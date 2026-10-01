( function ( $ ) {
	'use strict';

	$( function () {
		$( '.dic-color-field' ).wpColorPicker();

		var frame;
		var $id = $( '#dic_logo_id' );
		var $preview = $( '#dic_logo_preview' );

		$( '#dic_logo_pick' ).on( 'click', function ( e ) {
			e.preventDefault();
			if ( ! frame ) {
				frame = wp.media( {
					title: window.dicAdminSkinSettings.frameTitle,
					button: { text: window.dicAdminSkinSettings.frameButton },
					library: { type: 'image' },
					multiple: false
				} );
				frame.on( 'select', function () {
					var att = frame.state().get( 'selection' ).first().toJSON();
					$id.val( att.id );
					$preview.attr( 'src', att.url ).show();
				} );
			}
			frame.open();
		} );

		$( '#dic_logo_remove' ).on( 'click', function ( e ) {
			e.preventDefault();
			$id.val( '0' );
			$preview.hide().attr( 'src', '' );
		} );
	} );
}( jQuery ) );
