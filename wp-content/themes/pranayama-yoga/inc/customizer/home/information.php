<?php 
/**
* Information Section Theme Option.
*
* @package  pranayama_yoga
*/

 function pranayama_yoga_customize_register_information( $wp_customize ) {
    
    global $pranayama_yoga_options_posts;
    
    /** information Section */
    $wp_customize->add_section(
        'pranayama_yoga_information_settings',
        array(
            'title' => __( 'Information Section', 'pranayama-yoga' ),
            'priority' => 30,
            'panel' => 'pranayama_yoga_home_page_settings',
        )
    );
    
    /** Enable/Disable information Section */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_info_section',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_info_section',
        array(
            'label' => __( 'Enable Information Section', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_information_settings',
            'type' => 'checkbox',
        )
    );
      /** information Post One */
    $wp_customize->add_setting(
        'pranayama_yoga_information_post_one',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_information_post_one',
        array(
            'label' => __( 'Select Post One', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_information_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );
       /** information Post two */
    $wp_customize->add_setting(
        'pranayama_yoga_information_post_two',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_information_post_two',
        array(
            'label' => __( 'Select Post Two', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_information_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

       /** information Post three */
    $wp_customize->add_setting(
        'pranayama_yoga_information_post_three',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_information_post_three',
        array(
            'label' => __( 'Select Post Three', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_information_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

           /** information Post Four */
    $wp_customize->add_setting(
        'pranayama_yoga_information_post_four',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_information_post_four',
        array(
            'label' => __( 'Select Post Four', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_information_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );
}
add_action( 'customize_register', 'pranayama_yoga_customize_register_information');