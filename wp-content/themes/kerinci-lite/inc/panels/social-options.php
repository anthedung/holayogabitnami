<?php
// Set Panel ID
$panel_id = 'kerinci_lite_social_options';

// Set prefix
$prefix = 'kerinci_lite';

/***********************************************/
/**************** CONTACT US  ******************/
/***********************************************/

$wp_customize->add_panel( $panel_id,
    array(
        'priority'          => 109,
        'capability'        => 'edit_theme_options',
        'theme_supports'    => '',
        'title'             => __( 'Social Profile Links', 'kerinci-lite' ),
        'description'       => __( 'Control various options for contact us section from front page.', 'kerinci-lite' ),
    )
);

    /***********************************************/
    /************** Contact Details  ***************/
    /***********************************************/


    $wp_customize->add_section( $prefix.'_social_section' ,
        array(
            'title'         => __( 'Social Links', 'kerinci-lite' ),
            'description'   => __( 'These are the contact details displayed in the Contact us section from front page.', 'kerinci-lite' ),
            'priority'      => 3,
            'panel'         => $panel_id
        )
    );

    /* Facebook URL */
    $wp_customize->add_setting( $prefix.'_facebook_url',
        array(
            'sanitize_callback'  => 'esc_url_raw',
            'default'            =>  esc_url_raw('#'),
            'transport'          => 'postMessage'
        )
    );

    $wp_customize->add_control( $prefix.'_facebook_url',
        array(
            'label'          => __( 'Facebook URL', 'kerinci-lite' ),
            'description'    => __( 'Will be displayed in the contact section from front page.', 'kerinci-lite' ),
            'section'        => $prefix.'_social_section',
            'settings'       => $prefix.'_facebook_url',
            'priority'       => 10
        )
    );

    /* Twitter URL */
    $wp_customize->add_setting( $prefix.'_twitter_url',
        array(
            'sanitize_callback'  => 'esc_url_raw',
            'default'            =>  esc_url_raw('#'),
            'transport'          => 'postMessage'
        )
    );

    $wp_customize->add_control( $prefix.'_twitter_url',
        array(
            'label'          => __( 'Twitter URL', 'kerinci-lite' ),
            'description'    => __('Will be displayed in the contact section from front page.', 'kerinci-lite'),
            'section'        => $prefix.'_social_section',
            'settings'       => $prefix.'_twitter_url',
            'priority'       => 10
        )
    );

    /* LinkedIN URL */
    $wp_customize->add_setting( $prefix.'_linkedin_url',
        array(
            'sanitize_callback'  => 'esc_url_raw',
            'default'            => esc_url_raw('#'),
            'transport'          => 'postMessage'
        )
    );

    $wp_customize->add_control( $prefix.'_linkedin_url',
        array(
            'label'          => __( 'LinkedIN URL', 'kerinci-lite' ),
            'description'    => __('Will be displayed in the contact section from front page.', 'kerinci-lite'),
            'section'        => $prefix.'_social_section',
            'settings'       => $prefix.'_linkedin_url',
            'priority'       => 10
        )
    );

	/* Google+ URL */
	$wp_customize->add_setting( $prefix.'_googlep_url',
		array(
			'sanitize_callback'  => 'esc_url_raw',
			'default'            => esc_url_raw('#'),
			'transport'          => 'postMessage'
		)
	);

	$wp_customize->add_control( $prefix.'_googlep_url',
		array(
			'label'          => __( 'Google+ URL', 'kerinci-lite' ),
			'description'    => __('Will be displayed in the contact section from front page.', 'kerinci-lite'),
			'section'        => $prefix.'_social_section',
			'settings'       => $prefix.'_googlep_url',
			'priority'       => 10
		)
	);

	/* Pinterest URL */
	$wp_customize->add_setting( $prefix.'_pinterest_url',
		array(
			'sanitize_callback'  => 'esc_url_raw',
			'default'            => esc_url_raw('#'),
			'transport'          => 'postMessage'
		)
	);

	$wp_customize->add_control( $prefix.'_pinterest_url',
		array(
			'label'          => __( 'Pinterest URL', 'kerinci-lite' ),
			'description'    => __('Will be displayed in the contact section from front page.', 'kerinci-lite'),
			'section'        => $prefix.'_social_section',
			'settings'       => $prefix.'_pinterest_url',
			'priority'       => 10
		)
	);