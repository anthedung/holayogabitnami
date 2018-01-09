<?php 
/**
* news Section Theme Option.
*
* @package  pranayama_yoga
*/

 function pranayama_yoga_customize_register_blog( $wp_customize ) {

      /** blog Section */
    $wp_customize->add_section(
        'pranayama_yoga_blog_settings',
        array(
            'title' => __( 'Blog Section', 'pranayama-yoga' ),
            'priority' => 30,
            'panel' => 'pranayama_yoga_home_page_settings',
        )
    );
    
    /** Enable/Disable blog Section */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_blog_section',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_blog_section',
        array(
            'label' => __( 'Enable Blog Section', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_blog_settings',
            'type' => 'checkbox',
        )
    );

    /** blog section Title */
    $wp_customize->add_setting(
        'pranayama_yoga_blog_section_title',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_blog_section_title',
        array(
            'label' => __( 'Section Title', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_blog_settings',
            'type' => 'text',
        )
    );

      /** blog section description */
    $wp_customize->add_setting(
        'pranayama_yoga_blog_section_description',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_blog_section_description',
        array(
            'label' => __( 'Section Description', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_blog_settings',
            'type' => 'textarea',
        )
    );


}
add_action( 'customize_register', 'pranayama_yoga_customize_register_blog' );
