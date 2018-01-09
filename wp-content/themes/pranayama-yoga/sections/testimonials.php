<?php 
/**
* Testimonials Section
*
* @package pranayama_yoga
*/ 
    $testimonial_category = get_theme_mod('pranayama_yoga_testimonials_cat');
    
    if( $testimonial_category ):

        $testimonial_qry = new WP_Query( array(

		 	'post_type' => 'post',
		    
		    'posts_per_page' => -1,
		    
		    'cat' => $testimonial_category,
		    
		    'ignore_sticky_posts' => true
		    
		    ));
?>
		<div class="testimonial section-six">
	    	<div class="container">
				<div class="testimonial-holder">
			    	<div class="holder">
			    		<?php if( $testimonial_qry->have_posts()){ $i = 0; ?>
				            <div class="tab">
				                <?php while( $testimonial_qry->have_posts() ){ $testimonial_qry->the_post(); $i++; ?>
						            <div id="testimonial-<?php echo $i; ?>" class="testimonial-tab-content">
						                <?php the_content(); ?>
						            </div>
				                <?php } ?>
				            </div>
                        <div class="testimonial-tab-holder">
				    		<div class="testimonial-tabs-menu owl-carousel owl-theme">
				    		   <?php  $j=0;
					            while( $testimonial_qry->have_posts() ){ $testimonial_qry->the_post(); $j++; ?>
					    		    
					    		    	<a href="#testimonial-<?php echo $j; ?>" <?php if( $j == 2 ){ echo 'class="current"'; } ?>>
					    		    	   <?php if( has_post_thumbnail() ){
					    		    	   	the_post_thumbnail( 'pranayama-yoga-home-testimonial-thumb' );
					    		    	   	} ?>
					    		    	</a>
					    		    
					    		<?php 
					    		} ?>
				    		</div>
			    		</div>
			    		<?php } wp_reset_postdata(); ?>
			    	</div>
				</div>
			</div>
		</div>
	<?php endif;