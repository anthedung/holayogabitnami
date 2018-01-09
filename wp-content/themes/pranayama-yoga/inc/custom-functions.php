<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Pranayama_Yoga
 */

if ( ! function_exists( 'pranayama_yoga_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function pranayama_yoga_setup() {
  /*
   * Make theme available for translation.
   * Translations can be filed in the /languages/ directory.
   * If you're building a theme based on Pranayama Yoga, use a find and replace
   * to change 'pranayama-yoga' to the name of your theme in all the template files.
   */
  load_theme_textdomain( 'pranayama-yoga', get_template_directory() . '/languages' );

  // Add default posts and comments RSS feed links to head.
  add_theme_support( 'automatic-feed-links' );

  /*
   * Let WordPress manage the document title.
   * By adding theme support, we declare that this theme does not use a
   * hard-coded <title> tag in the document head, and expect WordPress to
   * provide it for us.
   */
  add_theme_support( 'title-tag' );

        /** Custom Logo */
  add_theme_support( 'custom-logo', array(        
        'header-text' => array( 'site-title', 'site-description' ),
    ) );

     //Add Excerpt support on page
  add_post_type_support( 'page', 'excerpt' );

  /*
   * Enable support for Post Thumbnails on posts and pages.
   *
   * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
   */
  add_theme_support( 'post-thumbnails' );

  // This theme uses wp_nav_menu() in one location.
  register_nav_menus( array(
    'primary' => esc_html__( 'Primary', 'pranayama-yoga' ),
  ) );

  /*
   * Switch default core markup for search form, comment form, and comments
   * to output valid HTML5.
   */
  add_theme_support( 'html5', array(
    'search-form',
    'comment-list',
    'gallery',
    'caption',
  ) );

  /*
   * Enable support for Post Formats.
   * See https://developer.wordpress.org/themes/functionality/post-formats/
   */
  add_theme_support( 'post-formats', array(
    'aside',
    'image',
    'gallery',
    'audio',
    'video',
    'quote',
    'link',
  ) );

  // Set up the WordPress core custom background feature.
  add_theme_support( 'custom-background', apply_filters( 'pranayama_yoga_custom_background_args', array(
    'default-color' => 'ffffff',
    'default-image' => '',
  ) ) );

  add_image_size( 'pranayama-yoga-blog-thumb', 750, 450, true);
  add_image_size( 'pranayama-yoga-banner-thumb', 1349, 434, true);
  add_image_size( 'pranayama-yoga-blog-full-width-thumb', 1170, 450, true);
  add_image_size( 'pranayama-yoga-about-thumb', 427, 279, true);
  add_image_size( 'pranayama-yoga-home-classes-thumb', 360, 250, true);
  add_image_size( 'pranayama-yoga-home-trainer-thumb', 360, 400, true);
  add_image_size( 'pranayama-yoga-home-testimonial-thumb', 83, 83, true);
  add_image_size( 'pranayama-yoga-home-blog-thumb', 360, 198, true);
  add_image_size( 'pranayama-yoga-reason-thumb', 59, 59, true);
  add_image_size( 'pranayama-yoga-widgets-thumb', 60, 60, true);


}
endif;

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function pranayama_yoga_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'pranayama_yoga_content_width', 750 );
}
/**
* Adjust content_width value according to template.
*
* @return void
*/
function pranayama_yoga_template_redirect_content_width() {

    // Full Width in the absence of sidebar.
    if( is_page() ){
       $sidebar_layout = pranayama_yoga_sidebar_layout();
       if( ( $sidebar_layout == 'no-sidebar' ) || ! ( is_active_sidebar( 'right-sidebar' ) ) ) $GLOBALS['content_width'] = 1170;
        
    }elseif( ! ( is_active_sidebar( 'right-sidebar' ) ) ) {
        $GLOBALS['content_width'] = 1170;
    }

}

/**
 * Enqueue scripts and styles.
 */
function pranayama_yoga_scripts() {
  $fonts = array();
  $fonts[] = 'Catamaran:400,100,500,600,700,800';
  $fonts[] = 'Nunito:700';
  $query_args = array(
    'family' => urlencode( implode( '|', $fonts ) ),
    );

  wp_enqueue_style( 'font-awesome', get_template_directory_uri(). '/css/font-awesome.css' );
  wp_enqueue_style( 'jquery-sidr-light', get_template_directory_uri(). '/css/jquery.sidr.light.css' );
  wp_enqueue_style( 'pranayama-yoga-google-fonts', add_query_arg( $query_args, "//fonts.googleapis.com/css" ));
  wp_enqueue_style( 'owl-carousel', get_template_directory_uri(). '/css/owl.carousel.css' );
  wp_enqueue_style( 'animate', get_template_directory_uri(). '/css/animate.css' );
     
  wp_enqueue_style( 'pranayama-yoga-style', get_stylesheet_uri(), array(), PRANAYAMA_YOGA_THEME_VERSION );

  wp_enqueue_script( 'jquery-sidr', get_template_directory_uri() . '/js/jquery.sidr.js', array(), '2.2.1', true );
  wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/js/owl.carousel.js', array(), '2.2.1', true );
  wp_enqueue_script( 'equal-height', get_template_directory_uri() . '/js/equal-height.js', array('jquery'), '0.7.0', PRANAYAMA_YOGA_THEME_VERSION, true );
  wp_register_script( 'pranayama-yoga-custom', get_template_directory_uri() . '/js/custom.js', array('jquery'), PRANAYAMA_YOGA_THEME_VERSION, true );
  $array = array(
        'rtl' => is_rtl(),
    );
    
  wp_localize_script( 'pranayama-yoga-custom', 'pranayama_yoga_data', $array );
  wp_enqueue_script( 'pranayama-yoga-custom' );


  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }
}

if ( is_admin() ) : // Load only if we are viewing an admin page

    function pranayama_yoga_admin_scripts() {
        
        wp_enqueue_style( 'pranayama-yoga-admin-style',get_template_directory_uri().'/inc/css/admin.css', '1.0', 'screen' );
        
    }

endif;

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function pranayama_yoga_body_classes( $classes ) {

   global $post;
    
    // Adds a class of group-blog to blogs with more than 1 published author.
    if ( is_multi_author() ) {
        $classes[] = 'group-blog';
    }

    // Adds a class of hfeed to non-singular pages.
    if ( ! is_singular() ) {
        $classes[] = 'hfeed';
    }

    // Adds a class of custom-background-image to sites with a custom background image.
    if ( get_background_image() ) {
        $classes[] = 'custom-background-image';
    }

    // Adds a class of custom-background-color to sites with a custom background color.
    if ( get_background_color() != 'ffffff' ) {
        $classes[] = 'custom-background-color';
    }
    
    if( is_404()){
        $classes[] = 'full-width';
    }

     if( !( is_active_sidebar( 'right-sidebar' ) ) ) {
        $classes[] = 'full-width'; 
    }
    
    if( is_page() ){
        $sidebar_layout = pranayama_yoga_sidebar_layout();
        if( $sidebar_layout == 'no-sidebar' )
        $classes[] = 'full-width';
    }

    return $classes;
}

/**
 * Hook to move comment text field to the bottom in WP 4.4
 * 
 * @link http://www.wpbeginner.com/wp-tutorials/how-to-move-comment-text-field-to-bottom-in-wordpress-4-4/  
 */
function pranayama_yoga_move_comment_field_to_bottom( $fields ) {
    $comment_field = $fields['comment'];
    unset( $fields['comment'] );
    $fields['comment'] = $comment_field;
    return $fields;
}

if ( ! function_exists( 'pranayama_yoga_excerpt_more' ) && ! is_admin() ) :
    /**
    * Replaces "[...]" (appended to automatically generated excerpts) with ... * 
    */
    function pranayama_yoga_excerpt_more() {
        return ' &hellip; ';
    }

endif;


if ( ! function_exists( 'pranayama_yoga_excerpt_length' ) ) :
    /**
    * Changes the default 55 character in excerpt 
    */
    function pranayama_yoga_excerpt_length( $length ) {
        return 50;
    }

endif;


/**
 * Flush out the transients used in pranayama_yoga_categorized_blog.
 */
function pranayama_yoga_category_transient_flusher() {
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }
  // Like, beat it. Dig?
  delete_transient( 'pranayama_yoga_categories' );
}

/**
 * Custom CSS
*/
if ( function_exists( 'wp_update_custom_css_post' ) ) {
    // Migrate any existing theme CSS to the core option added in WordPress 4.7.
    $css = get_theme_mod( 'pranayama_yoga_custom_css' );
    if ( $css ) {
        $core_css = wp_get_custom_css(); // Preserve any CSS already added to the core option.
        $return = wp_update_custom_css_post( $core_css . $css );
        if ( ! is_wp_error( $return ) ) {
            // Remove the old theme_mod, so that the CSS is stored in only one place moving forward.
            remove_theme_mod( 'pranayama_yoga_custom_css' );
        }
    }
} else {
    // Back-compat for WordPress < 4.7.
  function pranayama_yoga_custom_css(){
    $custom_css = get_theme_mod( 'pranayama_yoga_custom_css' );
    if( ! empty( $custom_css ) ){
      echo '<style type="text/css">';
      echo wp_strip_all_tags( $custom_css );
      echo '</style>';
    }
  }
  add_action( 'wp_head', 'pranayama_yoga_custom_css', 100 );
}

if( ! function_exists( 'pranayama_yoga_change_comment_form_default_fields' ) ) :
/**
 * Change Comment form default fields i.e. author, email & url.
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function pranayama_yoga_change_comment_form_default_fields( $fields ){
    
    // get the current commenter if available
    $commenter = wp_get_current_commenter();
 
    // core functionality
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );    
 
    // Change just the author field
    $fields['author'] = '<p class="comment-form-author"><input id="author" name="author" placeholder="' . esc_attr__( 'Name*', 'pranayama-yoga' ) . '" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>';
    
    $fields['email'] = '<p class="comment-form-email"><input id="email" name="email" placeholder="' . esc_attr__( 'Email*', 'pranayama-yoga' ) . '" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>';
    
    $fields['url'] = '<p class="comment-form-url"><input id="url" name="url" placeholder="' . esc_attr__( 'Website', 'pranayama-yoga' ) . '" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>'; 
    
    return $fields;
    
}
endif;

if( ! function_exists( 'pranayama_yoga_change_comment_form_defaults' ) ) :
/**
 * Change Comment Form defaults
 * https://blog.josemcastaneda.com/2016/08/08/copy-paste-hurting-theme/
*/
function pranayama_yoga_change_comment_form_defaults( $defaults ){
    
    // Change the "cancel" to "I would rather not comment" and use a span instead
    $defaults['comment_field'] = '<p class="comment-form-comment"><label for="comment"></label><textarea id="comment" name="comment" placeholder="' . esc_attr__( 'Comment', 'pranayama-yoga' ) . '" cols="45" rows="8" aria-required="true"></textarea></p>';
    
    $defaults['label_submit'] = esc_attr__( 'Submit', 'pranayama-yoga' );
    
    return $defaults;
    
}
endif;
