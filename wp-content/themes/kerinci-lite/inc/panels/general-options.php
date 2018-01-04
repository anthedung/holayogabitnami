<?php
// Set Panel ID
$panel_id = 'kerinci_panel_general';

// Set prefix
$prefix = 'kerinci_lite';


// Change panel for Colors
$site_colors        = $wp_customize->get_section( 'colors' );
$site_colors->panel = $panel_id;
$site_colors->title = __( 'Background Color', 'kerinci-lite' );

// Change panel for Background Image
$site_title        = $wp_customize->get_section( 'background_image' );
$site_title->panel = $panel_id;

// Change panel for Static Front Page
$site_title        = $wp_customize->get_section( 'static_front_page' );
$site_title->panel = $panel_id;

// Change Logo section
$site_logo              = $wp_customize->get_control( 'custom_logo' );
$site_logo->description = __( 'The site logo is used as a graphical representation of your company name. Recommended size: 105 (width) x 75 (height) pixels(px).', 'kerinci-lite' );
$site_logo->label       = __( 'Site logo', 'kerinci-lite' );
$site_logo->section     = $prefix . '_general_logo_section';
$site_logo->priority    = 1;

// Change site icon section
$site_icon           = $wp_customize->get_control( 'site_icon' );
$site_icon->section  = $prefix . '_general_logo_section';
$site_icon->priority = 2;

// Change panel for Static Front Page
$bocah        = $wp_customize->get_section( 'title_tagline' );
$bocah->panel = $panel_id;
$bocah->priority    = 1;





/***********************************************/
/************** GENERAL OPTIONS  ***************/
/***********************************************/


$wp_customize->add_panel( $panel_id, array(
	'priority'       => 1,
	'capability'     => 'edit_theme_options',
	'theme_supports' => '',
	'title'          => __( 'General Options', 'kerinci-lite' ),
	'description'    => __( 'You can change the site layout in this area as well as your contact details (the ones displayed in the header & footer) ', 'kerinci-lite' ),
) );

/***********************************************/
/*********** Logo section  ************/
/***********************************************/

$wp_customize->add_section( $prefix . '_general_logo_section', array(
	'title'    => __( 'Logo', 'kerinci-lite' ),
	'priority' => 2,
	'panel'    => $panel_id,
) );

/***********************************************/
/*********** General Site Settings  ************/
/***********************************************/


/* Company text logo */
$wp_customize->add_setting( $prefix . '_text_logo', array(
	'sanitize_callback' => 'kerinci_sanitize_html',
	'default'           => __( 'Kerinci', 'kerinci-lite' ),
	'transport'         => 'postMessage',
) );

$wp_customize->add_control( $prefix . '_text_logo', array(
	'label'       => __( 'Text logo (company name)', 'kerinci-lite' ),
	'description' => __( 'This field is best used when you don\'t have an image logo or simply prefer using a text as your logo / company name.', 'kerinci-lite' ),
	'section'     => $prefix . '_general_logo_section',
	'priority'    => 2,
) );


/***********************************************/
/************** Footer Details  ***************/
/***********************************************/
$wp_customize->add_section( $prefix . '_general_footer_section', array(
	'title'       => __( 'Copyright', 'kerinci-lite' ),
	'description' => __( 'Change the footer copyright message from here.', 'kerinci-lite' ),
	'priority'    => 4,
	'panel'       => $panel_id,
) );

/* Footer Copyright */
$wp_customize->add_setting( $prefix . '_footer_copyright', array(
	'sanitize_callback' => 'kerinci_lite_sanitize_html',
	'default'           => __( '&copy; Copyright 2016. All Rights Reserved.', 'kerinci-lite' ),
	'transport'         => 'postMessage',
) );

$wp_customize->add_control( $prefix . '_footer_copyright', array(
	'label'       => __( 'Footer Copyright', 'kerinci-lite' ),
	'description' => __( 'Use this to display your company copyright message.', 'kerinci-lite' ),
	'section'     => $prefix . '_general_footer_section',
	'priority'    => 2,
) );