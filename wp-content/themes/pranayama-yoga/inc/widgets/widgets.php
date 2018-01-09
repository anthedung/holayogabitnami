<?php 
/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */

function pranayama_yoga_widgets_init() {
  
    $sidebars = array(
        'sidebar'   => array(
            'name'        => esc_html__( 'Right Sidebar', 'pranayama-yoga' ),
            'id'          => 'right-sidebar', 
            'description' => esc_html__( 'Add widgets here.', 'pranayama-yoga' ),
        ),        
        'footer-one'=> array(
            'name'        => esc_html__( 'Footer One', 'pranayama-yoga' ),
            'id'          => 'footer-one', 
            'description' => esc_html__( 'Add footer one widgets.', 'pranayama-yoga' ),
        ),
        'footer-two'=> array(
            'name'        => esc_html__( 'Footer Two', 'pranayama-yoga' ),
            'id'          => 'footer-two', 
            'description' => esc_html__( 'Add footer two widgets.', 'pranayama-yoga' ),
        ),
        'footer-three'=> array(
            'name'        => esc_html__( 'Footer Three', 'pranayama-yoga' ),
            'id'          => 'footer-three', 
            'description' => esc_html__( 'Add footer three widgets.', 'pranayama-yoga' ),
        ),
        'footer-four'=> array(
            'name'        => esc_html__( 'Footer Four', 'pranayama-yoga' ),
            'id'          => 'footer-four', 
            'description' => esc_html__( 'Add footer four widgets.', 'pranayama-yoga' ),
        )
    );
    
    foreach( $sidebars as $sidebar ){
        register_sidebar( array(
        'name'          => $sidebar['name'],
        'id'            => $sidebar['id'],
        'description'   => $sidebar['description'],
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
      ) );
    }
    
}
add_action( 'widgets_init', 'pranayama_yoga_widgets_init' );

/**
 * Recent Post Widget
*/
require get_template_directory() . '/inc/widgets/widget-recent-post.php';

/**
 * Popular Post Widget
*/
require get_template_directory() . '/inc/widgets/widget-popular-post.php';

/**
 * Social Link Widget
*/
require get_template_directory() . '/inc/widgets/widget-social-links.php';
