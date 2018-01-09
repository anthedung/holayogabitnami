<?php
/**
 * Footer Theme Option.
 *
 * @package pranayama_yoga
 */

function pranayama_yoga_customize_register_footer( $wp_customize ) {

  /** Footer Section */
    $wp_customize->add_section(
        'pranayama_yoga_footer_section',
        array(
            'title' => __( 'Footer Settings', 'pranayama-yoga' ),
            'priority' => 70,
        )
    );
    
    /** Copyright Text */
    $wp_customize->add_setting(
        'pranayama_yoga_footer_copyright_text',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_footer_copyright_text',
        array(
            'label'       => __( 'Copyright Info', 'pranayama-yoga' ),
            'section'     => 'pranayama_yoga_footer_section',
            'description' => __( 'To create your own footer copyright. You can change it from here', 'pranayama-yoga' ),
            'type'        => 'textarea',
        )
    );
    
}
add_action( 'customize_register', 'pranayama_yoga_customize_register_footer' );