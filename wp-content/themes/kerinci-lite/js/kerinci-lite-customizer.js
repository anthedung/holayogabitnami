/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */
( function( $ ) {
	/* Company text logo */
	wp.customize( 'kerinci_text_logo', function( value ) {
		value.bind( function( newval ) {
			if( wp.customize._value.kerinci_img_logo() == '' ) {
				$( '#header .logo-title .site-title' ).html( newval );
			}
		} );
	} );

	/* Company image logo */
	wp.customize( 'kerinci_img_logo', function( value ) {
		value.bind( function( newval ) {
			if( newval !== '' ) {
				$( '#header .header-logo' ).empty();
				$( '#header .header-logo' ).prepend( '<img src="" alt="'+ wp.customize._value.kerinci_text_logo +'" title="'+ wp.customize._value.kerinci_text_logo +'" />' );
				$( '#header .header-logo img' ).attr( 'src', newval );
			} else {
				$( '#header .header-logo' ).text( wp.customize._value.kerinci_text_logo() );
			}
		} );
	} );

	/* Footer Copyright */
	wp.customize( 'kerinci_lite_footer_copyright', function( value ) {
		value.bind( function( newval ) {
			$( '#footer .copyright-footer' ).html( newval );
		} );
	} );

	wp.customize( 'kerinci_lite_general_footer_display_copyright', function( value ) {
		value.bind( function( newval ) {
			if( newval == true ) {
				$( '#footer .copyright-footer span[data-customizer="copyright-credit"]' ).removeClass( 'customizer-display-none' );
			} else if( newval == false ) {
				$( '#footer .copyright-footer span[data-customizer="copyright-credit"]' ).addClass( 'customizer-display-none' );
			}
		} );
	} );


	/* Facebook URL */
	wp.customize( 'kerinci_lite_facebook_url', function( value ) {
		value.bind( function( newval ) {
			$( '.social-profile a[title="Facebook"]' ).attr( 'href', newval );
		} );
	} );

	/* Twitter URL */
	wp.customize( 'kerinci_lite__twitter_url', function( value ) {
		value.bind( function( newval ) {
			$( '.social-profile a[title="Twitter"]' ).attr( 'href', newval );
		} );
	} );

	/* LinkedIn URL */
	wp.customize( 'kerinci_lite_linkedin_url', function( value ) {
		value.bind( function( newval ) {
			$( '.social-profile a[title="LinkedIn"]' ).attr( 'href', newval );
		} );
	} );

	/* Google URL */
	wp.customize( 'kerinci_lite_googlep_url', function( value ) {
		value.bind( function( newval ) {
			$( '.social-profile a[title="Googlep"]' ).attr( 'href', newval );
		} );
	} );

	/* LinkedIn URL */
	wp.customize( 'kerinci_lite_pinterest_url', function( value ) {
		value.bind( function( newval ) {
			$( '.social-profile a[title="Pinterest"]' ).attr( 'href', newval );
		} );
	} );

} )( jQuery );