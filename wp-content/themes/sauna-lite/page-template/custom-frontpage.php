<?php
/**
 * The template for displaying home page.
 *
 * Template Name: Custom Home Page
 *
 * @package Sauna Lite
 */

get_header(); ?>

<?php do_action( 'sauna_lite_before_slider' ); ?>

<?php /** slider section **/ ?>
	<?php
		// Get pages set in the customizer (if any)
		$pages = array();
		for ( $count = 1; $count <= 5; $count++ ) {
			$mod = absint( get_theme_mod( 'sauna_lite_slidersettings-page-' . $count ) );
			if ( 'page-none-selected' != $mod ) {
				$pages[] = $mod;
			}
		}
		if( !empty($pages) ) :
			$args = array(
				'posts_per_page' => 5,
				'post_type' => 'page',
				'post__in' => $pages,
				'orderby' => 'post__in'
			);
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) :
				$count = 1;
				?>
				<div class="slider-main">
					<div id="slider" class="nivoSlider">
						<?php
							$sauna_lite_n = 0;
						while ( $query->have_posts() ) : $query->the_post();
								
								$sauna_lite_n++;
								$sauna_lite_slideno[] = $sauna_lite_n;
								$sauna_lite_slidetitle[] = get_the_title();
								$sauna_lite_slidelink[] = esc_url(get_permalink());
								?>
									<img src="<?php the_post_thumbnail_url('full'); ?>" title="#slidecaption<?php echo esc_attr( $sauna_lite_n ); ?>" />
								<?php
							$count++;
						endwhile;
							wp_reset_postdata();
						?>
					</div>

					<?php
					$sauna_lite_k = 0;
				    foreach( $sauna_lite_slideno as $sauna_lite_sln ){ ?>
						<div id="slidecaption<?php echo esc_attr( $sauna_lite_sln ); ?>" class="nivo-html-caption">
							<div class="slide-cap  ">
								<div class="container">
									<h2><?php echo esc_html( $sauna_lite_slidetitle[$sauna_lite_k] ); ?></h2>
									<a class="read-more" href="<?php echo esc_url( $sauna_lite_slidelink[$sauna_lite_k] ); ?>"><?php esc_html_e( 'Learn More','sauna-lite' ); ?></a>
								</div>
							</div>
						</div>
			    	<?php $sauna_lite_k++;
					} ?>
				</div>
			<?php else : ?>
					<div class="header-no-slider"></div>
				<?php
			endif;
		else : ?>
				<div class="header-no-slider"></div>
			<?php
		endif;
	?>

<?php do_action( 'sauna_lite_after_slider' ); ?>

<?php /*--OUR SERVICES--*/?>
<section id="services-sec">
    <div class="container">
	    <div class="service-title">
	        <?php if( get_theme_mod('sauna_lite_sec1_title') != ''){ ?>     
	            <h3><?php echo esc_html(get_theme_mod('sauna_lite_sec1_title',__('Our Services','sauna-lite'))); ?></h3>  
	        <div class="images_border">
	            <img src="<?php echo esc_url( get_theme_mod('',get_template_directory_uri().'/images/heading-boder.png') ); ?>" alt="">
	        </div>
	        <?php }?>
	    </div>
		<?php $pages = array();
		for ( $count = 0; $count <= 3; $count++ ) {
			$mod = absint( get_theme_mod( 'sauna_lite_services_sec_ettings-page-' . $count ));
			if ( 'page-none-selected' != $mod ) {
			  $pages[] = $mod;
			}
		}
		if( !empty($pages) ) :
		  $args = array(
		    'post_type' => 'page',
		    'post__in' => $pages,
		    'orderby' => 'post__in'
		  );
		  $query = new WP_Query( $args );
		  if ( $query->have_posts() ) :
		    $count = 0;
				while ( $query->have_posts() ) : $query->the_post(); ?>
					<div class="col-md-4">
					    <div class="service-imagebox">
					        <img src="<?php the_post_thumbnail_url('full'); ?>"/>
					    </div>
					    <div class="service-content">
					        <h4><?php the_title(); ?></h4>
					        <p><?php the_excerpt(); ?></p>                  
					        <div class="clearfix"></div>
					    </div>
					</div>
				<?php $count++; endwhile; 
				wp_reset_postdata();
				?>
		  <?php else : ?>
		      <div class="no-postfound"></div>
		  <?php endif;
		endif;?>
	    <div class="clearfix"></div>
	</div> 
</section>

<?php do_action( 'sauna_lite_after_service' ); ?>

	
<?php get_footer(); ?>