<?php 
/**
* Testmonial Section Theme Option.
*
* @package  pranayama_yoga
*/

function pranayama_yoga_customize_register_testmonial( $wp_customize ) {

    global $pranayama_yoga_option_categories;
    
     /** testimonial Section */
    $wp_customize->add_section(
        'pranayama_yoga_testimonial_settings',
        array(
            'title' => __( 'Testimonial Section', 'pranayama-yoga' ),
            'priority' => 30,
            'panel' => 'pranayama_yoga_home_page_settings',
        )
    );
    
    /** Enable/Disable testimonial Section */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_testimonials_section',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_testimonials_section',
        array(
            'label' => __( 'Enable Testimonial Section', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_testimonial_settings',
            'type' => 'checkbox',
        )
    );

     /** Select Category */
    $wp_customize->add_setting(
        'pranayama_yoga_testimonials_cat',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_testimonials_cat',
        array(
            'label' => __( 'Choose Testimonials Category', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_testimonial_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_option_categories,
        )
    );
}
add_action( 'customize_register', 'pranayama_yoga_customize_register_testmonial' );
