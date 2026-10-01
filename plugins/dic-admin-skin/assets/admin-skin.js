( function () {
	'use strict';

	var cfg = window.dicAdminSkin;
	if ( ! cfg ) {
		return;
	}

	var order = [ 'auto', 'light', 'dark' ];
	var root = document.documentElement;

	function resolve( mode ) {
		if ( mode === 'auto' ) {
			return window.matchMedia( '(prefers-color-scheme: dark)' ).matches ? 'dark' : 'light';
		}
		return mode;
	}

	function apply( mode ) {
		root.setAttribute( 'data-dic-mode', mode );
		root.setAttribute( 'data-dic-theme', resolve( mode ) );
		var label = document.querySelector( '#wp-admin-bar-dic-mode-toggle .dic-mode-label' );
		if ( label ) {
			label.textContent = cfg.labels[ mode ];
		}
	}

	function save( mode ) {
		var body = new URLSearchParams( {
			action: 'dic_admin_skin_mode',
			nonce: cfg.nonce,
			mode: mode
		} );
		fetch( cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body
		} ).catch( function () {} );
	}

	document.addEventListener( 'click', function ( e ) {
		var link = e.target.closest( '#wp-admin-bar-dic-mode-toggle > a' );
		if ( ! link ) {
			return;
		}
		e.preventDefault();
		var current = root.getAttribute( 'data-dic-mode' ) || 'auto';
		var next = order[ ( order.indexOf( current ) + 1 ) % order.length ];
		apply( next );
		save( next );
	} );

	// Follow system changes while in auto mode.
	window.matchMedia( '(prefers-color-scheme: dark)' ).addEventListener( 'change', function () {
		if ( root.getAttribute( 'data-dic-mode' ) === 'auto' ) {
			apply( 'auto' );
		}
	} );
}() );
