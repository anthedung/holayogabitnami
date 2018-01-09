<?php 
/**
* Yoga Classes Section Theme Option.
*
* @package  pranayama_yoga
*/

 function pranayama_yoga_customize_register_yoga_classes( $wp_customize ) {
   
    global $pranayama_yoga_options_posts;
    
    /** Yoga Classes Section */
    $wp_customize->add_section(
        'pranayama_yoga_yoga_classes_settings',
        array(
            'title' => __( 'Yoga Classes Section', 'pranayama-yoga' ),
            'priority' => 30,
            'panel' => 'pranayama_yoga_home_page_settings',
        )
    );
    
    /** Enable/Disable yoga_classes Section */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_yogaclasses_section',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_yogaclasses_section',
        array(
            'label' => __( 'Enable Yoga Classes Section', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_yoga_classes_settings',
            'type' => 'checkbox',
        )
    );

    /** Newest class section Title */
    $wp_customize->add_setting(
        'pranayama_yoga_classes_section_title',
        array(
            'default' => '',
            'sanitize_callback' => 'sanitize_text_field',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_classes_section_title',
        array(
            'label' => __( 'Section Title', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_yoga_classes_settings',
            'type' => 'text',
        )
    );
    /** class Post One */
    $wp_customize->add_setting(
        'pranayama_yoga_class_post_one',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_class_post_one',
        array(
            'label' => __( 'Select Post One', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_yoga_classes_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );
       /** class Post two */
    $wp_customize->add_setting(
        'pranayama_yoga_class_post_two',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_class_post_two',
        array(
            'label' => __( 'Select Post Two', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_yoga_classes_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

       /** class Post three */
    $wp_customize->add_setting(
        'pranayama_yoga_class_post_three',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_class_post_three',
        array(
            'label' => __( 'Select Post Three', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_yoga_classes_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

    /** class Post Four */
    $wp_customize->add_setting(
        'pranayama_yoga_class_post_four',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_class_post_four',
        array(
            'label' => __( 'Select Post Four', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_yoga_classes_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );
    
    /** class Post five */
    $wp_customize->add_setting(
        'pranayama_yoga_class_post_five',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_class_post_five',
        array(
            'label' => __( 'Select Post Five', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_yoga_classes_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

    /** class Post six */
    $wp_customize->add_setting(
        'pranayama_yoga_class_post_six',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_select',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_class_post_six',
        array(
            'label' => __( 'Select Post Six', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_yoga_classes_settings',
            'type' => 'select',
            'choices' => $pranayama_yoga_options_posts,
        )
    );

}
add_action( 'customize_register', 'pranayama_yoga_customize_register_yoga_classes' );
