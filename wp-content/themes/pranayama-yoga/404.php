<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Pranayama_Yoga
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<div class="container">
			    
			    <div class="error-holder">
					<h1><?php esc_html_e( '404', 'pranayama-yoga'); ?></h1>
					<h2><?php esc_html_e( 'Page Not Found.', 'pranayama-yoga' ); ?></h2>
				    
				    <div class="page-content">
						<p><?php esc_html_e( 'Can not find what you need? Take a moment and do a search below or start form our', 'pranayama-yoga' ); ?>
						     <a href="<?php echo esc_url( home_url( '/' )); ?>"><?php echo esc_html_e('Home page','pranayama-yoga'); ?></a>
						</p>
		                <?php get_search_form(); ?>
					</div><!-- .page-content -->
			    </div><!-- .error-404 -->
			
			</div>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer();