<?php 
/**
 * Default Theme Option.
 *
 * @package pranayama_yoga
 */
 
function pranayama_yoga_customize_register_default( $wp_customize ) {
	
/* Default Settings*/
     $wp_customize->add_panel(
        'wp_default_panel',
        array( 
            'priority' => 10,
            'capability' => 'edit_theme_options',
            'theme_support' => '',
            'title' => __('Default Settings','pranayama-yoga'),
            'description' => __('Default section provided by WordPress customizer','pranayama-yoga'),
            )
        );

    $wp_customize->get_section( 'title_tagline' )->panel     = 'wp_default_panel';
    $wp_customize->get_section( 'colors' )->panel            = 'wp_default_panel';
    $wp_customize->get_section( 'background_image' )->panel  = 'wp_default_panel';
    $wp_customize->get_section( 'static_front_page' )->panel = 'wp_default_panel'; 


    $wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
    $wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
    $wp_customize->get_setting( 'background_color' )->transport = 'refresh';
    $wp_customize->get_setting( 'background_image' )->transport = 'refresh';

     /** Default Settings Ends */
}

add_action( 'customize_register', 'pranayama_yoga_customize_register_default' );