<?php

define('KERINCI_LITE_DIR', get_template_directory_uri());
define('KERINCI_LITE_TEMPLATE_DIR', get_template_directory());

//Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) )
	$content_width = 1200; /* pixels */

/*-----------------------------------------------------------------------------------*/
/*  SETUP THEME
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'kerinci_lite_setup' ) ) :
function kerinci_lite_setup(){

	// several theme support
	add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'custom-background' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form') );   
    add_theme_support( 'html5', array( 'gallery', 'caption' ) );	
    load_theme_textdomain( 'kerinci-lite', KERINCI_LITE_TEMPLATE_DIR .'/languages' );
    add_theme_support( "title-tag" );
    add_theme_support( 'custom-logo', array(
        'flex-width'  => true,
        'flex-height' => true,
    ) );


    /*******************************************/
    /*************  Welcome screen *************/
    /*******************************************/

    if ( is_admin() ) {

        require KERINCI_LITE_TEMPLATE_DIR . '/inc/admin/about-theme/about-theme.php';
    }
}
endif;
add_action('after_setup_theme', 'kerinci_lite_setup');


/*-----------------------------------------------------------------------------------*/
/*  SCRIPTS & STYLES
/*-----------------------------------------------------------------------------------*/

function kerinci_lite_scripts() {

//All necessary CSS
wp_enqueue_style( 'kerinci-lite-bootstrap', KERINCI_LITE_DIR .'/css/bootstrap.min.css', array(), null );
wp_enqueue_style( 'kerinci-lite-plugin-css', KERINCI_LITE_DIR .'/css/plugin.css', array(), null );
wp_enqueue_style( 'kerinci-lite-style', get_stylesheet_uri(), array( 'kerinci-lite-bootstrap','kerinci-lite-plugin-css' ) );
wp_enqueue_style( 'kerinci-lite-font', KERINCI_LITE_DIR .'/css/font.css', array(), null );

//All Necessary Script
wp_enqueue_script( 'kerinci-lite-modernizr', KERINCI_LITE_DIR. '/js/modernizr.js', array( 'jquery' ), '', false );
wp_enqueue_script( 'kerinci-lite-respond', KERINCI_LITE_DIR. '/js/respond.js', array( 'jquery' ), '', false );
wp_enqueue_script( 'kerinci-lite-fitvids', KERINCI_LITE_DIR. '/js/fitVids.js', array( 'jquery' ), '', true );
wp_enqueue_script( 'kerinci-smartmenus', KERINCI_LITE_DIR. '/js/smartmenus.js', array( 'jquery' ), '', false );
wp_enqueue_script( 'kerinci-lite-main-js', KERINCI_LITE_DIR. '/js/main.js', array( 'jquery', 'kerinci-smartmenus' ), '', true );
}

add_action( 'wp_enqueue_scripts', 'kerinci_lite_scripts' );

add_action( 'wp_enqueue_scripts', 'kerinci_lite_comment_reply' );
function kerinci_lite_comment_reply(){
        if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
                wp_enqueue_script( 'comment-reply' );
        }
        
}

/*-----------------------------------------------------------------------------------*/
/*  SCRIPTS & STYLES
/*-----------------------------------------------------------------------------------*/

function kerinci_lite_font_setup() {
    $fonts_url = '';
 
    /* Translators: If there are characters in your language that are not
    * supported by Abel, translate this to 'off'. Do not translate
    * into your own language.
    */
    $abel = _x( 'on', 'Abel font: on or off', 'kerinci-lite' );
 
    /* Translators: If there are characters in your language that are not
    * supported by Carter One, translate this to 'off'. Do not translate
    * into your own language.
    */
    $carter = _x( 'on', 'Carter One font: on or off', 'kerinci-lite' );
 
    if ( 'off' !== $abel || 'off' !== $carter ) {
        $font_families = array();
 
        if ( 'off' !== $abel ) {
            $font_families[] = 'Abel:400,700,400italic';
        }
 
        if ( 'off' !== $carter ) {
            $font_families[] = 'Carter One:700italic,400,800,600';
        }
 
        $query_args = array(
            'family' => urlencode( implode( '|', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
        );
 
        $fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
    }
 
    return esc_url_raw( $fonts_url );
}

function kerinci_lite_font_scripts() {
    wp_enqueue_style( 'kerinci-slug-fonts', kerinci_lite_font_setup(), array(), null );
}
add_action( 'wp_enqueue_scripts', 'kerinci_lite_font_scripts' );


/*-----------------------------------------------------------------------------------*/
/*  MENU
/*-----------------------------------------------------------------------------------*/

//Register Menus
add_action( 'after_setup_theme', 'kerinci_lite_register_my_menu' );
function kerinci_lite_register_my_menu() {
  register_nav_menu( 'header-menu', __( 'Header Menu', 'kerinci-lite' ) );
}

// TOP MENU
function kerinci_lite_top_nav_menu(){
  wp_nav_menu( array(
    'theme_location' => 'header-menu',
    'container'       => 'ul',
   'menu_class'      => 'sm sm-clean',
    'fallback_cb'  => 'kerinci_lite_header_menu_cb'
  ));
}

// FALLBACK IF PRIMARY MENU HAVEN'T SET YET
function kerinci_lite_header_menu_cb() {
  echo '<ul class="sm sm-clean">';
  wp_list_pages('title_li=');
  echo '</ul>';
}

/*-----------------------------------------------------------------------------------*/
/*  WIDGET
/*-----------------------------------------------------------------------------------*/

// SETUP DEFAULT SIDEBAR
function kerinci_lite_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Primary Sidebar', 'kerinci-lite' ),
		'id'            => 'primary-sidebar',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'kerinci_lite_widgets_init' );

/*-----------------------------------------------------------------------------------*/
/*  CUSTOM FUNCTIONS
/*-----------------------------------------------------------------------------------*/

// INCLUDE SUPPORT FILE
require_once( KERINCI_LITE_TEMPLATE_DIR . '/inc/function/navigation.php' );
require_once( KERINCI_LITE_TEMPLATE_DIR . '/inc/function/comment.php' );
require_once( KERINCI_LITE_TEMPLATE_DIR . '/inc/function/custom.php');
require_once( KERINCI_LITE_TEMPLATE_DIR . '/inc/function/themeta.php' );
require_once( KERINCI_LITE_TEMPLATE_DIR . '/inc/function/thecontent.php' );
require_once( KERINCI_LITE_TEMPLATE_DIR . '/inc/function/customizer.php');