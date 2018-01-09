<?php 
/**
* About Section
*
* @package pranayama_yoga
*/
    $about_post     = get_theme_mod( 'pranayama_yoga_about_post_one' ); 
    $about_readmore = get_theme_mod( 'pranayama_yoga_about_readmore', __('Learn More', 'pranayama-yoga' ) );  

    echo '<div class="section-one">';

	    if($about_post){

	        $about_qry = new WP_Query( array( 'p' => $about_post ));
	        
	        if ( $about_qry -> have_posts()) : $about_qry->the_post(); ?>
	            
	            <div class="container">
				    <div class="row">
					    <div class="text-holder">
						    
						    <h2 class="title"><?php the_title(); ?></h2>
						    <?php  the_excerpt(); ?>
						    
						    <a href="<?php the_permalink(); ?>" class="btn">
						        <?php echo esc_html( $about_readmore ); ?>
						    </a>
					    </div>
				
				        <?php  
				        if( has_post_thumbnail() ){ ?>
					        <div class="img-holder">
					            <?php the_post_thumbnail( 'pranayama-yoga-about-thumb' ); ?>
					        </div>
				        <?php
				        } ?>
				    
				    </div>
			    </div>
		    <?php 
		    endif; 
		    wp_reset_postdata();  
		}
	echo '</div>';

