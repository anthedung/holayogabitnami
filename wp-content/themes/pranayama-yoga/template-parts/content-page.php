<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package pranayama_yoga
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <?php 
    /**
     * Before Page entry content
     * 
     * @hooked pranayama_yoga_page_content_image 
    */
    do_action( 'pranayama_yoga_before_page_entry_content' );    
    ?>
    
	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'pranayama-yoga' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
		pranayama_yoga_entry_footer();
		?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
