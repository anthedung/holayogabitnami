<?php
/**
* Banner Section
*
* @package pranayama_yoga
*/ 
    $banner_post     = get_theme_mod( 'pranayama_yoga_banner_post_one' );
    $banner_readmore = get_theme_mod( 'pranayama_yoga_banner_readmore', __('Learn More', 'pranayama-yoga' ) );  
    
    echo '<div class="banner">';
	    if($banner_post){
	        $banner_qry = new WP_Query( array( 'p' => $banner_post ) );
	        
	        if ( $banner_qry -> have_posts() ) :
	            $banner_qry->the_post(); 
	            
	            if(has_post_thumbnail()){ 
				    the_post_thumbnail( 'pranayama-yoga-banner-thumb' ); 
		?>
					<div class="banner-text">
						<div class="container">
							<div class="text-holder">
								<strong class="title"><?php the_title(); ?></strong>
							    <?php the_excerpt();  ?>
					            <a href="<?php the_permalink(); ?>" class="btn">
					                <?php echo esc_html( $banner_readmore ); ?>
					            </a>
							</div>
						</div>
					</div>
			    <?php 
			    }

		    endif; 
		    wp_reset_postdata();
	    }
	echo '</div>';