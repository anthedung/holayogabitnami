<?php 
/**
* Trainer Section Theme Option.
*
* @package  pranayama_yoga
*/

 function pranayama_yoga_customize_register_trainer( $wp_customize ) {
    
    global $pranayama_yoga_options_posts;
      /** trainer Section */
    $wp_customize->add_section(
        'pranayama_yoga_trainer_settings',
        array(
            'title' => __( 'Team Section', 'pranayama-yoga' ),
            'priority' => 30,
            'panel' => 'pranayama_yoga_home_page_settings',
        )
    );
    
    /** Enable/Disable trainer Section */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_trainer_section',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_trainer_section',
        array(
            'label' => __( 'Enable Team Section', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_trainer_settings',
            'type' => 'checkbox',
        )
    );

    /** Trainer section Title */
    $wp_customize->add_setting(
        'pranayama_yoga_trainer_section_title',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_trainer_section_title',
        array(
            'label' => __( 'Section Title', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_trainer_settings',
            'type' => 'text',
        )
    );
    
     /** staff Post One */
    $wp_customize->add_setting(
        'pranayama_yoga_trainer_post_one',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_trainer_post_one',
        array(
            'label' => __( 'Select Post One', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_trainer_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

     /** staff Post two */
    $wp_customize->add_setting(
        'pranayama_yoga_trainer_post_two',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_trainer_post_two',
        array(
            'label' => __( 'Select Post Two', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_trainer_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

    /** staff Post three */
    $wp_customize->add_setting(
        'pranayama_yoga_trainer_post_three',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_trainer_post_three',
        array(
            'label' => __( 'Select Post Three', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_trainer_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

}
add_action( 'customize_register', 'pranayama_yoga_customize_register_trainer' );
