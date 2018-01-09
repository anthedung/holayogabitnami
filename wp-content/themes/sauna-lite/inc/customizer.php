<?php
/**
 * Sauna Lite Theme Customizer
 *
 * @package Sauna Lite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function sauna_lite_customize_register( $wp_customize ) {

	//add home page setting pannel
	$wp_customize->add_panel( 'sauna_lite_panel_id', array(
	    'priority' => 10,
	    'capability' => 'edit_theme_options',
	    'theme_supports' => '',
	    'title' => __( 'Theme Settings', 'sauna-lite' ),
	    'description' => __( 'Description of what this panel does.', 'sauna-lite' )
	) );

	$wp_customize->add_section( 'sauna_lite_left_right', array(
    	'title'      => __( 'Layout Settings', 'sauna-lite' ),
		'priority'   => 30,
		'panel' => 'sauna_lite_panel_id'
	) );

	// Add Settings and Controls for Layout
	$wp_customize->add_setting('sauna_lite_theme_options',array(
	        'default' => '',
	        'sanitize_callback' => 'sauna_lite_sanitize_choices'	        
	    )
    );

	$wp_customize->add_control('sauna_lite_theme_options',
	    array(
	        'type' => 'radio',
	        'label' => 'Do you want this section',
	        'section' => 'sauna_lite_left_right',
	        'choices' => array(
	            'Left Sidebar' => __('Left Sidebar','sauna-lite'),
	            'Right Sidebar' => __('Right Sidebar','sauna-lite'),
	            'One Column' => __('One Column','sauna-lite'),
	            'Three Columns' => __('Three Columns','sauna-lite'),
	            'Four Columns' => __('Four Columns','sauna-lite'),
	            'Grid Layout' => __('Grid Layout','sauna-lite')
	        ),
	    )
    );

	//home page slider
	$wp_customize->add_section( 'sauna_lite_slidersettings' , array(
    	'title'      => __( 'Slider Settings', 'sauna-lite' ),
		'priority'   => 30,
		'panel' => 'sauna_lite_panel_id'
	) );

	for ( $count = 1; $count <= 4; $count++ ) {

		// Add color scheme setting and control.
		$wp_customize->add_setting( 'sauna_lite_slidersettings-page-' . $count, array(
			'default'           => '',
			'sanitize_callback' => 'absint'
		) );

		$wp_customize->add_control( 'sauna_lite_slidersettings-page-' . $count, array(
			'label'    => __( 'Select Slide Image Page', 'sauna-lite' ),
			'section'  => 'sauna_lite_slidersettings',
			'type'     => 'dropdown-pages'
		) );

	}

	//OUR services
	$wp_customize->add_section('sauna_lite_our_services',array(
		'title'	=> __('Our Services','sauna-lite'),
		'description'=> __('This section will appear below the slider.','sauna-lite'),
		'panel' => 'sauna_lite_panel_id',
	));	
	
	$wp_customize->add_setting('sauna_lite_sec1_title',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('sauna_lite_sec1_title',array(
		'label'	=> __('Section Title','sauna-lite'),
		'section'=> 'sauna_lite_our_services',
		'setting'=> 'sauna_lite_sec1_title',
		'type'=> 'text'
	));

	for ( $count = 0; $count <= 2; $count++ ) {

		$wp_customize->add_setting( 'sauna_lite_services_sec_ettings-page-' . $count, array(
			'default'           => '',
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_control( 'sauna_lite_services_sec_ettings-page-' . $count, array(
			'label'    => __( 'Select Service Page', 'sauna-lite' ),
			'section'  => 'sauna_lite_our_services',
			'type'     => 'dropdown-pages'
		));
	}	
}
add_action( 'customize_register', 'sauna_lite_customize_register' );


/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class sauna_lite_customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'sauna_lite_customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new sauna_lite_customize_Section_Pro(
				$manager,
				'example_1',
				array(
					'title'    => esc_html__( 'Sauna Pro', 'sauna-lite' ),
					'pro_text' => esc_html__( 'Go Pro',         'sauna-lite' ),
					'pro_url'  => 'https://www.themescaliber.com/premium/sauna-spa-wordpress-theme'
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'sauna-lite-customize-controls', trailingslashit( get_template_directory_uri() ) . '/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'sauna-lite-customize-controls', trailingslashit( get_template_directory_uri() ) . '/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
sauna_lite_customize::get_instance();