<?php
/**
 * Pranayama Yoga Theme Customizer.
 *
 * @package pranayama_yoga
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
 function pranayama_yoga_modify_sections( $wp_customize ){
    if ( version_compare( get_bloginfo('version'),'4.9', '>=') ) {
        $wp_customize->get_section( 'static_front_page' )->title = __( 'Static Front Page', 'lawyer-landing-page-pro' );
    }
}
add_action( 'customize_register', 'pranayama_yoga_modify_sections' );

$pranayama_yoga_sections = array( 'banners', 'about', 'information', 'yogaclasses', 'promotional', 'trainer', 'testimonials', 'blog', 'reason', 'subscription' );

$pranayama_yoga_settings = array( 'default', 'header', 'home', 'breadcrumb', 'social', 'footer', 'custom' );

/* Option list of all post */	
$pranayama_yoga_options_posts = array();
$options_posts_obj = get_posts('posts_per_page=-1');
$pranayama_yoga_options_posts[''] = __( 'Choose Post', 'pranayama-yoga' );
foreach ( $options_posts_obj as $posts ) {
	$pranayama_yoga_options_posts[$posts->ID] = $posts->post_title;
}

 /** Option list of all pages */ 
$args = array(
    'post_type' => 'page',
    'posts_per_page' => -1
);
$pranayama_yoga_options_pages = array();
$options_pages_obj = get_posts( $args );
$pranayama_yoga_options_pages[''] = __( 'Choose Page', 'pranayama-yoga' );
foreach ( $options_pages_obj as $p ) {
    $pranayama_yoga_options_pages[$p->ID] = $p->post_title; 
}

/* Option list of all categories */
$args = array(
   'type'                     => 'post',
   'orderby'                  => 'name',
   'order'                    => 'ASC',
   'hide_empty'               => 1,
   'hierarchical'             => 1,
   'taxonomy'                 => 'category'
); 
$pranayama_yoga_option_categories = array();
$category_lists = get_categories( $args );
$pranayama_yoga_option_categories[''] = __( 'Choose Category', 'pranayama-yoga' );
foreach( $category_lists as $category ){
    $pranayama_yoga_option_categories[$category->term_id] = $category->name;
}
 

foreach( $pranayama_yoga_settings as $setting ){
    require get_template_directory() . '/inc/customizer/' . $setting . '.php';
}

foreach( $pranayama_yoga_sections as $section ){
    require get_template_directory() . '/inc/customizer/home/' . $section . '.php';
}

/**
 * Sanitization Functions
*/
require get_template_directory() . '/inc/customizer/sanitization-functions.php';



/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function pranayama_yoga_customize_preview_js() {
    wp_enqueue_script( 'pranayama_yoga_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'pranayama_yoga_customize_preview_js' );
