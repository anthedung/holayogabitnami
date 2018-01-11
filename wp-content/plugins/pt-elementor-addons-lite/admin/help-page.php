<?php
/**
 * Get some constants ready for paths when your plugin grows
 *
 * @author nofearinc
 *
 * @package Get some constants ready for paths when your plugin grows.
 */

	?>
	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="wrap">
	<div id="icon-plugins" class="icon32"></div>
	<!-- <h2><?php _e( 'PT Plugin Base', 'ptbase' ); ?></h2> -->

	<div class="pt-help-page">		
		<div class="content alignleft">
			<div class="main-h2-heading">
			<h2 class='page-welcome'>PT Elementor <span>Addons Lite Settings</span></h2>
			</div>
			<div id="pt-help-content">

					<h3 id="demo"></h3>
					<h2 ><?php// _e( 'Base plugin page', 'ptbase' ); ?></h2>
						
					<p><?php //_e( 'Sample base plugin page', 'ptbase' ); ?></p>
					
					<form id="pt-plugin-base-form" action="options.php" method="POST">
						
							<?php settings_fields( 'pt_setting' ); ?>
							<?php  do_settings_sections( 'pt-plugin-base' ); ?>
							
							<input type="submit" id="smbt" class="smbt" value="<?php _e( 'Save', 'ptbase' ); ?>" />
					</form> <!-- end of #pttemplate-form -->
				
			</div>

			<footer class='pt-footer'>
			</footer>

		</div>
		<div class="sidebar alignright">
			<h2>Show Your Love!</h2>
			<div class="border-bottom"></div>
			<div class="social-icon">
				<a href="https://www.facebook.com/sharer/sharer.php?u=https%3A//wordpress.org/plugins/pt-elementor-addons-lite/" target="_blank"><i class="fa fa-facebook-square"  aria-hidden="true"></i></a>
				<a href="https://twitter.com/home?status=https%3A//wordpress.org/plugins/pt-elementor-addons-lite/" target="_blank"><i class="fa fa-twitter-square"  aria-hidden="true"></i></a>
				<a href="https://plus.google.com/share?url=https%3A//wordpress.org/plugins/pt-elementor-addons-lite/" target="_blank"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>
				<a href="https://www.linkedin.com/shareArticle?mini=true&url=https%3A//wordpress.org/plugins/pt-elementor-addons-lite/&title=&summary=&source=" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>
				<a href="https://pinterest.com/pin/create/button/?url=&media=https%3A//wordpress.org/plugins/pt-elementor-addons-lite/&description=" target="_blank"><i class="fa fa-pinterest-square" aria-hidden="true"></i></a>
			</div>
		</div>
		<div class="sidebar alignright">
			<h2>Get Support & Follow Us</h2>
			<div class="border-bottom1"></div>
			<div class="social-icon">
				<a href="https://wordpress.org/support/plugin/pt-elementor-addons-lite" target="_blank"><i class="fa fa-wordpress" aria-hidden="true"></i></a>
				<a href="https://www.facebook.com/groups/335254883593121/ " target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
				<a href="https://twitter.com/ParamThemes" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
				
			</div>
			
		</div>
		
	</div>
	
</div>

<script>

	$( "#smbt" ).click(function() {
	deleteMember();
	});
	function deleteMember() {
		swal({
    title: "Settings Saved!",
    type: "success",
	showCancelButton: false,
  showConfirmButton: false
})
	}

</script>
