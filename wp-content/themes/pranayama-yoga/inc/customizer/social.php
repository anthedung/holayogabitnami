<?php
/**
* Social Link Theme Option.
*
* @package pranayama_yoga
*/

function pranayama_yoga_customize_register_social( $wp_customize ) {
   
    /** Social Settings */
    $wp_customize->add_section(
        'pranayama_yoga_social_settings',
        array(
            'title' => __( 'Social Settings', 'pranayama-yoga' ),
            'description' => __( 'Leave blank if you do not want to show the social link.', 'pranayama-yoga' ),
            'priority' => 50,
            'capability' => 'edit_theme_options',
        )
    );
    
    /** Enable/Disable Social in Header */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_social',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_social',
        array(
            'label' => __( 'Enable Social Links in Header', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'checkbox',
        )
    );

    /** Enable/Disable Social in footer */
    $wp_customize->add_setting(
        'pranayama_yoga_ed_social_footer',
        array(
            'default' => '',
            'sanitize_callback' => 'pranayama_yoga_sanitize_checkbox',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ed_social_footer',
        array(
            'label' => __( 'Enable Social Links in Footer', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'checkbox',
        )
    );
    
    /** Facebook */
    $wp_customize->add_setting(
        'pranayama_yoga_facebook',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_facebook',
        array(
            'label' => __( 'Facebook', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );
    
    /** Twitter */
    $wp_customize->add_setting(
        'pranayama_yoga_twitter',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_twitter',
        array(
            'label' => __( 'Twitter', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );
    
    /** Google Plus */
    $wp_customize->add_setting(
        'pranayama_yoga_google_plus',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_google_plus',
        array(
            'label' => __( 'Google Plus', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );
    
    /** LinkedIn */
    $wp_customize->add_setting(
        'pranayama_yoga_linkedin',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_linkedin',
        array(
            'label' => __( 'LinkedIn', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );

    /** Pinterest */
    $wp_customize->add_setting(
        'pranayama_yoga_pinterest',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_pinterest',
        array(
            'label' => __( 'Pinterest', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );
    
    /** Instagram */
    $wp_customize->add_setting(
        'pranayama_yoga_instagram',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_instagram',
        array(
            'label' => __( 'Instagram', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );
    
    /** YouTube */
    $wp_customize->add_setting(
        'pranayama_yoga_youtube',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_youtube',
        array(
            'label' => __( 'YouTube', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );

    /** Ok */
    $wp_customize->add_setting(
        'pranayama_yoga_ok',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_ok',
        array(
            'label' => __( 'OK', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );
    /** Vk */
    $wp_customize->add_setting(
        'pranayama_yoga_vk',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_vk',
        array(
            'label' => __( 'VK', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );

    /** Xing */
    $wp_customize->add_setting(
        'pranayama_yoga_xing',
        array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        )
    );
    
    $wp_customize->add_control(
        'pranayama_yoga_xing',
        array(
            'label' => __( 'Xing', 'pranayama-yoga' ),
            'section' => 'pranayama_yoga_social_settings',
            'type' => 'text',
        )
    );


}
add_action( 'customize_register', 'pranayama_yoga_customize_register_social' );