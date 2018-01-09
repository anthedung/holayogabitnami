<?php 
/**
* promotional Section Theme Option.
*
* @package  pranayama_yoga
*/

 function pranayama_yoga_customize_register_promotional( $wp_customize ) {
  
      /** promotional Section */
    $wp_customize->add_section(
        'pranayama_yoga_promotional_settings',
        array(
            'title' => __( 'Promotional Section', 'pranayama-yoga' ),
            'priority' => 30,
            'panel' => 'pranayama_yoga_home_page_settings',
        )
    );
    
    /** Enable/Disable promotional Section */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_promotional_section',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_promotional_section',
        array(
            'label' => __( 'Enable Promotional Section', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_promotional_settings',
            'type' => 'checkbox',
        )
    );

    $wp_customize->add_setting(
        'pranayama_yoga_promotional_section_title',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_promotional_section_title',
        array(
            'label' => __( 'Section Title', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_promotional_settings',
            'type' => 'text',
        )
    );


    $wp_customize->add_setting(
        'pranayama_yoga_promotional_section_description',
        array(
            'default' => '',
            'sanitize_callback' => 'wp_kses_post',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_promotional_section_description',
        array(
            'label' => __( 'Section Description', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_promotional_settings',
            'type' => 'textarea',
        )
    );
    $wp_customize->add_setting(
        'pranayama_yoga_call_to_action_button',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_call_to_action_button',
        array(
            'label' => __( 'Call To Action Button Text', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_promotional_settings',
            'type' => 'text',
        )
    );

    $wp_customize->add_setting(
        'pranayama_yoga_call_to_action_button_link',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_call_to_action_button_link',
        array(
            'label' => __( 'Link Here', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_promotional_settings',
            'type' => 'text',
        )
    );
  

}
add_action( 'customize_register', 'pranayama_yoga_customize_register_promotional' );
