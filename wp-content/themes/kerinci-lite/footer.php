
</div> <!-- #main-->

<?php 
if ( current_user_can( 'edit_theme_options' ) ) {
	$footer_copyright  = get_theme_mod( 'kerinci_lite_footer_copyright', __( '&copy; Copyright 2016. All Rights Reserved.', 'kerinci-lite' ) );
} else {
	$footer_copyright  = get_theme_mod( 'kerinci_lite_footer_copyright' );
}
?>

<div class="container">
<footer id="footer" class="site-footer col-md-12" role="contentinfo">

<p class="copyright-footer text-center">
	<span data-customizer="copyright-credit"><a href="<?php $kerinci_lite_theme = wp_get_theme(); echo $kerinci_lite_theme->get( 'ThemeURI' ); ?>"><?php echo $kerinci_lite_theme->get( 'Name' ); ?></a> <?php esc_html_e( 'by', 'kerinci-lite' ); ?> <a href="<?php echo $kerinci_lite_theme->get( 'AuthorURI' ); ?>" rel="nofollow"><?php echo $kerinci_lite_theme->get( 'Author' ); ?></a></span>
	<span><?php echo kerinci_lite_sanitize_html( $footer_copyright ); ?></span>
</p>


</footer><!-- #footer -->
</div>
<?php wp_footer(); ?>

</body>
</html>