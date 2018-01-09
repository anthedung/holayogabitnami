<?php 
/**
* About Section Theme Option.
*
* @package  pranayama_yoga
*/

 function pranayama_yoga_customize_register_about( $wp_customize ) {

    global $pranayama_yoga_options_posts;

     /** about Section */
    $wp_customize->add_section(
        'pranayama_yoga_about_settings',
        array(
            'title' => __( 'About Section', 'pranayama-yoga' ),
            'priority' => 30,
            'panel' => 'pranayama_yoga_home_page_settings',
        )
    );
    
    /** Enable/Disable about Section */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_about_section',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_about_section',
        array(
            'label' => __( 'Enable About Section', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_about_settings',
            'type' => 'checkbox',
        )
    );
    /** about Post One */
    $wp_customize->add_setting(
        'pranayama_yoga_about_post_one',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_about_post_one',
        array(
            'label' => __( 'Select Post', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_about_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

    /** About Section Readmore */
    $wp_customize->add_setting(
        'pranayama_yoga_about_readmore',
        array(
            'default' => __( 'Learn More', 'pranayama-yoga' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_about_readmore',
        array(
            'label' => __( 'Read More Text', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_about_settings',
            'type' => 'text',
        )
    );

}
add_action( 'customize_register', 'pranayama_yoga_customize_register_about' );
