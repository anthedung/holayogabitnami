<?php 
/**
* Banner Section Theme Option.
*
* @package  pranayama_yoga
*/

function pranayama_yoga_customize_register_banner( $wp_customize ) {
    
    global $pranayama_yoga_options_posts;

      /** banner Section */
    $wp_customize->add_section(
        'pranayama_yoga_banner_settings',
        array(
            'title' => __( 'Banner Section', 'pranayama-yoga' ),
            'priority' => 30,
            'panel' => 'pranayama_yoga_home_page_settings',
        )
    );
    
    /** Enable/Disable banner Section */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_slider_section',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_slider_section',
        array(
            'label' => __( 'Enable Banner Section', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_banner_settings',
            'type' => 'checkbox',
        )
    );

    /** Banner Post One */
    $wp_customize->add_setting(
        'pranayama_yoga_banner_post_one',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_banner_post_one',
        array(
            'label' => __( 'Select Banner Post', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_banner_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );
    
    /** banner Readmore */
    $wp_customize->add_setting(
        'pranayama_yoga_banner_readmore',
        array(
            'default' => __( 'Learn More', 'pranayama-yoga' ),
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_banner_readmore',
        array(
            'label' => __( 'Readmore Text', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_banner_settings',
            'type' => 'text',
        )
    );

    /** banner Settings Ends */

}
add_action( 'customize_register', 'pranayama_yoga_customize_register_banner' );
