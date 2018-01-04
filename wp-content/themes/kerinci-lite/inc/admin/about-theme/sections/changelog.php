<?php
/**
 * Changelog
 */

$kerinci = wp_get_theme( 'kerinci-lite' );

?>
<div class="kerinci-tab-pane" id="changelog">

	<div class="kerinci-tab-pane-center">
	
		<h1><?php echo esc_attr( $kerinci['Name'] ); ?> <?php if( !empty($kerinci['Version']) ): ?> <sup id="kerinci-theme-version"><?php echo esc_attr( $kerinci['Version'] ); ?> </sup><?php endif; ?></h1>

	</div>

	<?php
	WP_Filesystem();
	global $wp_filesystem;
	$kerinci_changelog = $wp_filesystem->get_contents( get_template_directory().'/changelog.txt' );
	$kerinci_changelog_lines = explode(PHP_EOL, $kerinci_changelog);
	foreach($kerinci_changelog_lines as $kerinci_changelog_line){
		if(substr( $kerinci_changelog_line, 0, 3 ) === "###"){
			echo '<hr /><h1>'.substr($kerinci_changelog_line,3).'</h1>';
		} else {
			echo $kerinci_changelog_line,'<br/>';
		}
	}

	?>
	
</div>