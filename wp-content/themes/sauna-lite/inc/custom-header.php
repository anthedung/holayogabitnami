<?php
/**
 * @package Sauna Lite
 * @subpackage sauna-lite
 * @since sauna-lite 1.0
 * Setup the WordPress core custom header feature.
 *
 * @uses sauna_lite_header_style()
*/

function sauna_lite_custom_header_setup() {

	add_theme_support( 'custom-header', apply_filters( 'sauna_lite_custom_header_args', array(

		//'default-image'          => get_template_directory_uri().'/images/banner_bg.jpg',

		'default-text-color'     => 'fff',
		'header-text' 			 =>	false,
		'width'                  => 1600,
		'height'                 => 400,
		'wp-head-callback'       => 'sauna_lite_header_style',
	) ) );

}

add_action( 'after_setup_theme', 'sauna_lite_custom_header_setup' );

if ( ! function_exists( 'sauna_lite_header_style' ) ) :

/**
 * Styles the header image and text displayed on the blog
 *
 * @see sauna_lite_custom_header_setup().
 */

function sauna_lite_header_style() {
	$header_text_color = get_header_textcolor();
	?>
	<style type="text/css">
	<?php
		//Check if user has defined any header image.
		if ( get_header_image() ) :
	?>
		#header{
			background: url(<?php echo esc_url(get_header_image()); ?>) no-repeat;
			background-position: center top;
		}
	<?php endif; ?>	
	</style>
	<?php
}

endif; // sauna_lite_header_style

add_action( 'admin_head', 'sauna_lite_admin_header_css' );
function sauna_lite_admin_header_css(){ ?>
	<style>pre{white-space: pre-wrap;}</style><?php
}
