<?php
/**
 * BreadCrumb Theme Option.
 *
 * @package pranayama_yoga
 */

function pranayama_yoga_customize_register_breadcrumb( $wp_customize ) {
     
    /** BreadCrumb Settings */
    $wp_customize->add_section(
        'pranayama_yoga_breadcrumb_settings',
        array(
            'title' => __( 'Breadcrumb Settings', 'pranayama-yoga' ),
            'priority' => 40,
            'capability' => 'edit_theme_options',
        )
    );
   

    /** Enable/Disable BreadCrumb */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_breadcrumb',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_breadcrumb',
        array(
            'label' => __( 'Enable Breadcrumb', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_breadcrumb_settings',
            'type' => 'checkbox',
        )
    );
    
    /** Show/Hide Current */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_current',
        array(
            'default' => '1',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_current',
        array(
            'label' => __( 'Show current', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_breadcrumb_settings',
            'type' => 'checkbox',
        )
    );
    
    /** Home Text */
    $wp_customize->add_setting(
        'pranayama_yoga_breadcrumb_home_text',
        array(
            'default' => __( 'Home', 'pranayama-yoga' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_breadcrumb_home_text',
        array(
            'label' => __( 'Breadcrumb Home Text', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_breadcrumb_settings',
            'type' => 'text',
        )
    );
    
    /** Breadcrumb Separator */
    $wp_customize->add_setting(
        'pranayama_yoga_breadcrumb_separator',
        array(
            'default' => __( '>', 'pranayama-yoga' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_breadcrumb_separator',
        array(
            'label' => __( 'Breadcrumb Separator', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_breadcrumb_settings',
            'type' => 'text',
        )
    );
    /** BreadCrumb Settings Ends */

}
add_action( 'customize_register', 'pranayama_yoga_customize_register_breadcrumb' );
