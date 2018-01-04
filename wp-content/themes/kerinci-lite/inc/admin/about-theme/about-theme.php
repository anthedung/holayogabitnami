<?php
/**
 * About Theme Kerinci
 */
class kerinci_about_theme {

	/**
	 * Constructor for the about theme section
	 */
	public function __construct() {

		/* create dashbord page */
		add_action( 'admin_menu', array( $this, 'kerinci_about_register_menu' ) );

		/* activation notice */
		add_action( 'load-themes.php', array( $this, 'kerinci_activation_admin_notice' ) );

		/* enqueue script and style for about theme section */
		add_action( 'admin_enqueue_scripts', array( $this, 'kerinci_about_style_and_scripts' ) );

		/* enqueue script for customizer */
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'kerinci_about_scripts_for_customizer' ) );

		/* load about theme section */
		add_action( 'kerinci_about', array( $this, 'kerinci_about_getting_started' ), 	    10 );
		add_action( 'kerinci_about', array( $this, 'kerinci_about_translate' ), 		            40 );
		add_action( 'kerinci_about', array( $this, 'kerinci_about_changelog' ), 				50 );

		/* ajax callback for dismissable required actions */
		add_action( 'wp_ajax_kerinci_lite_dismiss_required_action', array( $this, 'kerinci_dismiss_required_action_callback') );

	}

	/**
	 * Creates the dashboard page
	 * @see  add_theme_page()
	 * @since 1.8.2.4
	 */
	public function kerinci_about_register_menu() {
		add_theme_page( 'About Kerinci', 'About Kerinci', 'activate_plugins', 'kerinci-about', array( $this, 'kerinci_about_screen' ) );
	}

	/**
	 * Adds an admin notice upon successful activation.
	 * @since 1.8.2.4
	 */
	public function kerinci_activation_admin_notice() {
		global $pagenow;

		if ( is_admin() && ('themes.php' == $pagenow) && isset( $_GET['activated'] ) ) {
			add_action( 'admin_notices', array( $this, 'kerinci_about_admin_notice' ), 99 );
		}
	}

	/**
	 * Display an admin notice linking to the about theme section
	 * @since 1.8.2.4
	 */
	public function kerinci_about_admin_notice() {
		?>
			<div class="updated notice is-dismissible">
				<p><?php echo sprintf( esc_html__( 'Welcome! Thank you for choosing Kerinci! To fully take advantage of the best our theme can offer please make sure you visit our %swelcome page%s.', 'kerinci-lite' ), '<a href="' . esc_url( admin_url( 'themes.php?page=kerinci-about' ) ) . '">', '</a>' ); ?></p>
				<p><a href="<?php echo esc_url( admin_url( 'themes.php?page=kerinci-about' ) ); ?>" class="button" style="text-decoration: none;"><?php _e( 'Get started with Kerinci', 'kerinci-lite' ); ?></a></p>
			</div>
		<?php
	}

	/**
	 * Load about theme section css and javascript
	 * @since  1.8.2.4
	 */
	public function kerinci_about_style_and_scripts( $hook_suffix ) {

		if ( 'appearance_page_kerinci-about' == $hook_suffix ) {
			wp_enqueue_style( 'kerinci-about-theme-css', get_template_directory_uri() . '/inc/admin/about-theme/css/about.css' );
			wp_enqueue_script( 'kerinci-about-theme-js', get_template_directory_uri() . '/inc/admin/about-theme/js/about.js', array('jquery') );

		}
	}

	/**
	 * Welcome screen content
	 * @since 1.8.2.4
	 */
	public function kerinci_about_screen() {

		?>

		<ul class="kerinci-nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#getting_started" aria-controls="getting_started" role="tab" data-toggle="tab"><?php esc_html_e( 'Getting started','kerinci-lite'); ?></a></li>
			<li role="presentation"><a href="#translate" aria-controls="translate" role="tab" data-toggle="tab"><?php esc_html_e( 'Contribute','kerinci-lite'); ?></a></li>
			<li role="presentation"><a href="#changelog" aria-controls="changelog" role="tab" data-toggle="tab"><?php esc_html_e( 'Changelog','kerinci-lite'); ?></a></li>
		</ul>

		<div class="kerinci-tab-content">

			<?php
			/**
			 * @hooked kerinci_about_getting_started - 10
			 * @hooked kerinci_about_actions_required - 20
			 * @hooked kerinci_about_child_themes - 30
			 * @hooked kerinci_about_translate - 40
			 * @hooked kerinci_about_changelog - 50
			 */
			do_action( 'kerinci_about' ); ?>

		</div>
		<?php
	}

	/**
	 * Getting started
	 * @since 1.8.2.4
	 */
	public function kerinci_about_getting_started() {
		require_once( get_template_directory() . '/inc/admin/about-theme/sections/getting-started.php' );
	}

	/**
	 * Contribute
	 * @since 1.8.2.4
	 */
	public function kerinci_about_translate() {
		require_once( get_template_directory() . '/inc/admin/about-theme/sections/translate.php' );
	}

	/**
	 * Changelog
	 * @since 1.8.2.4
	 */
	public function kerinci_about_changelog() {
		require_once( get_template_directory() . '/inc/admin/about-theme/sections/changelog.php' );
	}
}

new kerinci_about_theme();
