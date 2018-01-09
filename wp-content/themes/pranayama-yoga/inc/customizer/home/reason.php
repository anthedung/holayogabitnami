<?php 
/**
* Reason Section Theme Option.
*
* @package  pranayama_yoga
*/

 function pranayama_yoga_customize_register_reason( $wp_customize ) {
    
    global $pranayama_yoga_options_posts, $pranayama_yoga_options_pages;
    /** Reason Section */
    $wp_customize->add_section(
        'pranayama_yoga_reason_settings',
        array(
            'title' => __( 'Reasons Section', 'pranayama-yoga' ),
            'priority' => 30,
            'panel' => 'pranayama_yoga_home_page_settings',
        )
    );
    
    /** Enable/Disable reason Section */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_reason_section',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_reason_section',
        array(
            'label' => __( 'Enable Reasons Section', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_reason_settings',
            'type' => 'checkbox',
        )
    );
    
     /** reason Section Title */
    $wp_customize->add_setting(
        'pranayama_yoga_reason_section_page',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_reason_section_page',
        array(
            'label' => __( 'Select Page', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_reason_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_pages
        )
    );

     /**  Post One */
    $wp_customize->add_setting(
        'pranayama_yoga_reason_post_one',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_reason_post_one',
        array(
            'label' => __( 'Select Post One', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_reason_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

     /**  Post two */
    $wp_customize->add_setting(
        'pranayama_yoga_reason_post_two',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_reason_post_two',
        array(
            'label' => __( 'Select Post Two', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_reason_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

    /**  Post three */
    $wp_customize->add_setting(
        'pranayama_yoga_reason_post_three',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_reason_post_three',
        array(
            'label' => __( 'Select Post Three', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_reason_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

        /**  Post One */
    $wp_customize->add_setting(
        'pranayama_yoga_reason_post_four',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_reason_post_four',
        array(
            'label' => __( 'Select Post Four', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_reason_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

     /**  Post five */
    $wp_customize->add_setting(
        'pranayama_yoga_reason_post_five',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_reason_post_five',
        array(
            'label' => __( 'Select Post Five', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_reason_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

    /**  Post six */
    $wp_customize->add_setting(
        'pranayama_yoga_reason_post_six',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_reason_post_six',
        array(
            'label' => __( 'Select Post Six', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_reason_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );  

}
add_action( 'customize_register', 'pranayama_yoga_customize_register_reason' );
