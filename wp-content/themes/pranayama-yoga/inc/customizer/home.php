<?php
/**
 * Home Page Theme Option.
 *
 * @package pranayama_yoga
 */

function pranayama_yoga_customize_register_home( $wp_customize ) {
    
    
     /** Home Page Settings */
    $wp_customize->add_panel( 
        'pranayama_yoga_home_page_settings',
         array(
            'priority' => 30,
            'capability' => 'edit_theme_options',
            'title' => __( 'Home Page Settings', 'pranayama-yoga' ),
            'description' => __( 'Customize Home Page Settings', 'pranayama-yoga' ),
        ) 
    );

}
add_action( 'customize_register', 'pranayama_yoga_customize_register_home' );