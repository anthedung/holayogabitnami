<?php
/**
 * Sauna Lite functions and definitions
 *
 * @package Sauna Lite
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */

if ( ! function_exists( 'sauna_lite_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
 
/* Breadcrumb Begin */
function sauna_lite_the_breadcrumb() {
	if (!is_home()) {
		echo '<a href="';
			echo esc_url( home_url() );
		echo '">';
			bloginfo('name');
		echo "</a> ";
		if (is_category() || is_single()) {
			the_category('title_li=');
			if (is_single()) {
				echo "<span> ";
					the_title();
				echo "</span> ";
			}
		} elseif (is_page()) {
			the_title();
		}
	}
}

/* Theme Setup */
function sauna_lite_setup() {

	$GLOBALS['content_width'] = apply_filters( 'sauna_lite_content_width', 640 );
	
	load_theme_textdomain( 'sauna-lite', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'woocommerce' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'custom-logo', array(
		'height'      => 240,
		'width'       => 240,
		'flex-height' => true,
	) );
	add_image_size('sauna-lite-homepage-thumb',240,145,true);
	
       register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'sauna-lite' ),
	) );

	add_theme_support( 'custom-background', array(
		'default-color' => 'f1f1f1'
	) );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, icons, and column width.
	 */
	add_editor_style( array( 'css/editor-style.css', sauna_lite_font_url() ) );

}
endif;
add_action( 'after_setup_theme', 'sauna_lite_setup' );

/* Theme Widgets Setup */
function sauna_lite_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Blog Sidebar', 'sauna-lite' ),
		'description'   => __( 'Appears on blog page sidebar', 'sauna-lite' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	
	register_sidebar( array(
		'name'          => __( 'Page Sidebar', 'sauna-lite' ),
		'description'   => __( 'Appears on page sidebar', 'sauna-lite' ),
		'id'            => 'sidebar-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Thrid Column Sidebar', 'sauna-lite' ),
		'description'   => __( 'Appears on page sidebar', 'sauna-lite' ),
		'id'            => 'sidebar-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Navigation 1', 'sauna-lite' ),
		'description'   => __( 'Appears on footer', 'sauna-lite' ),
		'id'            => 'footer-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Navigation 2', 'sauna-lite' ),
		'description'   => __( 'Appears on footer', 'sauna-lite' ),
		'id'            => 'footer-2',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Navigation 3', 'sauna-lite' ),
		'description'   => __( 'Appears on footer', 'sauna-lite' ),
		'id'            => 'footer-3',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer Navigation 4', 'sauna-lite' ),
		'description'   => __( 'Appears on footer', 'sauna-lite' ),
		'id'            => 'footer-4',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

}
add_action( 'widgets_init', 'sauna_lite_widgets_init' );

/* Theme Font URL */
function sauna_lite_font_url() {
		$font_url = '';
		
		/* Translators: If there are any character that are
		* not supported by PT Sans, translate this to off, do not
		* translate into your own language.
		*/
		$ptsans = _x('on', 'PT Sans font:on or off','sauna-lite');
		
		/* Translators: If there are any character that are
		* not supported by Roboto, translate this to off, do not
		* translate into your own language.
		*/
		$roboto = _x('on', 'Roboto font:on or off','sauna-lite');
		
		/* Translators: If there are any character that are
		* not supported by Roboto Condensed, translate this to off, do not
		* translate into your own language.
		*/
		$roboto_cond = _x('on', 'Roboto Condensed font:on or off','sauna-lite');
		
		/* Translators: If there are any character that are
		* not supported by Roboto, translate this to off, do not
		* translate into your own language.
		*/
		$DancingScript = _x('on', 'Dancing Script:on or off','sauna-lite');

		/* Translators: If there are any character that are
		* not supported by Roboto, translate this to off, do not
		* translate into your own language.
		*/
		$Montserrat = _x('on', 'Montserrat:on or off','sauna-lite');

		if('off' !== $ptsans || 'off' !==  $roboto || 'off' !== $roboto_cond){
			$font_family = array();
			
			if('off' !== $ptsans){
				$font_family[] = 'PT Sans:300,400,600,700,800,900';
			}
			
			if('off' !== $roboto){
				$font_family[] = 'Roboto:400,700';
			}
			
			if('off' !== $roboto_cond){
				$font_family[] = 'Roboto Condensed:400,700';
			}
			if('off' !== $DancingScript){
				$font_family[] = 'Dancing Script';
			}			
			if('off' !== $Montserrat){
				$font_family[] = 'Montserrat:300,400,600,700,800,900';
			}

			$query_args = array(
				'family'	=> urlencode(implode('|',$font_family)),
			);
			
			$font_url = add_query_arg($query_args,'//fonts.googleapis.com/css');
		}		
	return $font_url;
}
	
/* Theme enqueue scripts */
function sauna_lite_scripts() {
	wp_enqueue_style( 'sauna-lite-font', sauna_lite_font_url(), array() );	
	wp_enqueue_style( 'bootstrap', get_template_directory_uri().'/css/bootstrap.css' );
	wp_enqueue_style( 'sauna-lite-basic-style', get_stylesheet_uri() );
	wp_style_add_data( 'sauna-lite-style', 'rtl', 'replace' );
	wp_enqueue_style( 'sauna-lite-effect', get_template_directory_uri().'/css/effect.css' );
	wp_enqueue_style( 'font-awesome', get_template_directory_uri().'/css/font-awesome.css' );
	wp_enqueue_style( 'sauna-lite-customcss', get_template_directory_uri() . '/css/custom.css' );

	wp_enqueue_style( 'jquery-nivo-slider', get_template_directory_uri().'/css/nivo-slider.css' );
	wp_enqueue_script( 'jquery-nivo-slider', get_template_directory_uri() . '/js/jquery.nivo.slider.js', array('jquery') );
	wp_enqueue_script( 'sauna-lite-custom-front', get_template_directory_uri() . '/js/custom-front.js', array('jquery') ,'',true);
	
	wp_enqueue_script( 'sauna-lite-customscripts', get_template_directory_uri() . '/js/custom.js', array('jquery') );
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	/* Enqueue the Dashicons script */
	wp_enqueue_style( 'dashicons' );
	
	/** Load our IE-only stylesheet for all versions of IE.
	*   <!--[if lt IE 9]> ... <![endif]-->
	*
	*  Note: It is also possible to just check and see if the $is_IE global in WordPress is set to true before
	*  calling the wp_enqueue_style() function. If you are trying to load a stylesheet for all browsers
	*  EXCEPT for IE, then you would HAVE to check the $is_IE global since WordPress doesn't have a way to
	*  properly handle non-IE conditional comments.
	*/
	wp_enqueue_style('sauna-lite-ie', get_template_directory_uri().'/css/ie.css', array('sauna-lite-ie-style'));
	wp_style_add_data( 'sauna-lite-ie', 'conditional', 'IE' );
}
add_action( 'wp_enqueue_scripts', 'sauna_lite_scripts' );

//footer
define('sauna_lite_CREDIT','https://www.themescaliber.com','sauna-lite');

if ( ! function_exists( 'sauna_lite_credit' ) ) {
	function sauna_lite_credit(){
			echo "<a href=".esc_url(sauna_lite_CREDIT)." target='_blank' rel='nofollow'>ThemesCaliber</a>";
	}
}

//footer
define('sauna_lite_CREDIT1','https://www.themescaliber.com/premium/sauna-spa-wordpress-theme','sauna-lite');

if ( ! function_exists( 'sauna_lite_credit1' ) ) {
	function sauna_lite_credit1(){
			echo "<a href=".esc_url(sauna_lite_CREDIT1)." target='_blank' rel='nofollow'>Sauna WordPress Theme</a>";
	}
}

/*radio button sanitization*/

 function sauna_lite_sanitize_choices( $input, $setting ) {

    global $wp_customize; 

    $control = $wp_customize->get_control( $setting->id ); 

    if ( array_key_exists( $input, $control->choices ) ) {

        return $input;

    } else {

        return $setting->default;

    }
}

// mailing Function

	if ( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
	// do your stuff
	$url = get_site_url();
	// The message
	$message = "A Free Sauna Theme is activated on $url ";

	// In case any of our lines are larger than 70 characters, we should use wordwrap()
	$message = wordwrap($message, 70, "\r\n");

	// Send
	wp_mail('themescaliber@gmail.com', 'Theme Activated', $message);
}


/* Custom template tags for this theme. */
require get_template_directory() . '/inc/template-tags.php';

/* Customizer additions. */
require get_template_directory() . '/inc/customizer.php';

/* Customizer additions. */
require get_template_directory() . '/inc/custom-header.php';

?>