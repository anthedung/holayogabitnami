<?php 
/**
* 
* Custom CSS Theme Option. 
* @package pranayama_yoga  
*/

function pranayama_yoga_customize_register_custom( $wp_customize ) {
    
    /** Custom CSS*/
    if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
        $wp_customize->add_section(
            'pranayama_yoga_custom_settings',
            array(
                'title' => __( 'Custom CSS Settings', 'pranayama-yoga' ),
                'priority' => 50,
                'capability' => 'edit_theme_options',
            )
        );
        
        $wp_customize->add_setting(
            'pranayama_yoga_custom_css',
            array(
                'default' => '',
                'capability'        => 'edit_theme_options',
                'sanitize_callback' => 'wp_strip_all_tags'
                )
        );
        
        $wp_customize->add_control(
            'pranayama_yoga_custom_css',
            array(
                'label' => __( 'Custom Css', 'pranayama-yoga' ),
                'section' => 'pranayama_yoga_custom_settings',
                'description' => __( 'Put your custom CSS', 'pranayama-yoga' ),
                'type' => 'textarea',
            )
        );
    }
    /** Custom CSS Ends */
    
}
add_action( 'customize_register', 'pranayama_yoga_customize_register_custom' );