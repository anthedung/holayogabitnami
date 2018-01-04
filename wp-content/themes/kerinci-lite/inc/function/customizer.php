<?php 

/*
*
*	Theme Customizer Options
*	------------------------------------------------
*	Kerinci Framework
* 	Copyright Themes Awesome 2013 - http://www.themesawesome.com
*
*	kerinci_customize_register()
*	kerinci_customize_preview()
*
*/
	
if (!function_exists('kerinci_customize_register')) {
	function kerinci_customize_register($wp_customize) {
		
		$wp_customize->get_setting('blogname')->transport='postMessage';
		$wp_customize->get_setting('blogdescription')->transport='postMessage';
		$wp_customize->get_setting('header_textcolor')->transport='postMessage';
		$wp_customize->get_setting( 'header_image' )->transport = 'postMessage';
		$wp_customize->get_setting( 'header_image_data' )->transport = 'postMessage';

		$wp_customize->get_control( 'custom_logo' )->section = 'kerinci_general_section';

		// Colors Controls
		require_once KERINCI_LITE_TEMPLATE_DIR . '/inc/panels/general-options.php';

		// Socials Controls
		require_once KERINCI_LITE_TEMPLATE_DIR . '/inc/panels/social-options.php';

		// Extend Controls
		require_once KERINCI_LITE_TEMPLATE_DIR . '/inc/panels/extend-options.php';

	}
	add_action( 'customize_register', 'kerinci_customize_register' );
}


/**
*  Customizer Live Preview
*/
	function kerinci_lite_customizer_live_preview() {
	wp_enqueue_script( 'kerinci-lite-customizer',	KERINCI_LITE_DIR.'/js/kerinci-lite-customizer.js', array( 'jquery','customize-preview' ), NULL, true);
	}
	add_action( 'customize_preview_init', 'kerinci_lite_customizer_live_preview' );

/**
 *  Sanitize HTML
 */
if ( ! function_exists( 'kerinci_lite_sanitize_html' ) ) {
	function kerinci_lite_sanitize_html( $input ) {
		$input = force_balance_tags( $input );

		$allowed_html = array(
			'a'      => array(
				'href'  => array(),
				'title' => array(),
			),
			'br'     => array(),
			'em'     => array(),
			'img'    => array(
				'alt'    => array(),
				'src'    => array(),
				'srcset' => array(),
				'title'  => array(),
			),
			'strong' => array(),
		);
		$output       = wp_kses( $input, $allowed_html );

		return $output;
	}
}