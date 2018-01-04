<?php
/**
 * Getting started template
 */

$customizer_url = wp_customize_url() ;
?>

<div id="getting_started" class="kerinci-tab-pane active">

<div class="content-info-about">

	<div class="intro-head">
		<h1 class="kerinci-welcome-title"><?php _e('Welcome to Kerinci!', 'kerinci-lite'); ?> <?php if( !empty($kerinci_lite['Version']) ): ?> <sup id="kerinci-theme-version"><?php echo esc_attr( $kerinci_lite['Version'] ); ?> </sup><?php endif; ?></h1>
		<p><?php esc_html_e( 'We want to make sure you have the best experience using kerinci and that is why we gathered here all the necessary information for you. We hope you will enjoy using kerinci, as much as we enjoy creating great products.', 'kerinci-lite' ); ?>
	</div>

	<div class="kerinci-tab-pane-center column column-3">
		<div class="inner-info">
			<h1><?php esc_html_e( 'Getting started', 'kerinci-lite' ); ?></h1>

			<h4><?php esc_html_e( 'Customize everything in a single place.' ,'kerinci-lite' ); ?></h4>
			<p><?php esc_html_e( 'Using the WordPress Customizer you can easily customize every aspect of the theme.', 'kerinci-lite' ); ?></p>
			<p><a href="<?php echo esc_url( $customizer_url ); ?>" class="button button-primary"><?php esc_html_e( 'Go to Customizer', 'kerinci-lite' ); ?></a></p>
		</div>
	</div>

	<div class="kerinci-tab-pane-center column column-3">
		<div class="inner-info">
			<h1><?php esc_html_e( 'Need more features?', 'kerinci-lite' ); ?></h1>

			<h4><?php esc_html_e( 'Check our add on plugin for this theme.' ,'kerinci-lite' ); ?></h4>
			<p><?php esc_html_e( 'Check out the Premium version of this theme which comes with additional features and advanced customization.', 'kerinci-lite' ); ?></p>
			<p><a href="<?php $my_theme = wp_get_theme(); echo $my_theme->get( 'AuthorURI' ); ?>/products/wordpress-themes/kerinci/" class="button button-primary"><?php esc_html_e( 'Learn Premium Version', 'kerinci-lite' ); ?></a></p>
		</div>
	</div>

	<div class="kerinci-tab-pane-center column column-3">
		<div class="inner-info">
			<h1><?php esc_html_e( 'Documentation', 'kerinci-lite' ); ?></h1>

			<h4><?php esc_html_e( 'How to install this theme with a minutes.' ,'kerinci-lite' ); ?></h4>
			<p><?php esc_html_e( 'Please read our online documentation page to setup this theme. Install from clean WordPress within a minutes.', 'kerinci-lite' ); ?></p>
			<p><a href="https://themesawesome.zendesk.com/hc/en-us/categories/200201014-Kerinci" class="button button-primary"><?php esc_html_e( 'Read Documentation', 'kerinci-lite' ); ?></a></p>
		</div>
	</div>
</div>
</div>
