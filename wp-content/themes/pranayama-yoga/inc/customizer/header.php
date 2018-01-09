<?php
/**
 * Header Theme Option.
 *
 * @package pranayama_yoga
 */

function pranayama_yoga_customize_register_header( $wp_customize ) {

    /** Header Info Section */
    $wp_customize->add_section(
        'pranayama_yoga_header_info',
        array(
            'title' => __( 'Header Info', 'pranayama-yoga' ),
            'priority' => 20,
            'capability' => 'edit_theme_options',
        )
    );

     /** Address  */
    $wp_customize->add_setting(
        'pranayama_yoga_address',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_address',
        array(
            'label' => __( 'Address', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_header_info',
            'type' => 'text',
        )
    );

    /** Text for phone number  */
    $wp_customize->add_setting(
        'pranayama_yoga_text',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_text',
        array(
            'label' => __( 'Text for Phone Number', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_header_info',
            'type' => 'text',
        )
    );
    
    /** Phone Number  */
    $wp_customize->add_setting(
        'pranayama_yoga_phone',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_phone',
        array(
            'label' => __( 'Phone Number', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_header_info',
            'type' => 'text',
        )
    );
    
}
add_action( 'customize_register', 'pranayama_yoga_customize_register_header' );