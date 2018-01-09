<?php 
/**
* Trainer Section
*
* @package pranayama_yoga
*/
    $section_title   = get_theme_mod( 'pranayama_yoga_trainer_section_title' );
    $trainer_one     = get_theme_mod( 'pranayama_yoga_trainer_post_one' );
    $trainer_two     = get_theme_mod( 'pranayama_yoga_trainer_post_two' );
    $trainer_three   = get_theme_mod( 'pranayama_yoga_trainer_post_three' );
    
    $trainers_posts  = array( $trainer_one, $trainer_two, $trainer_three );
    $trainers_posts  = array_diff( array_unique( $trainers_posts ), array('') ); ?>
    
    <section class="section-five">
	    <div class="container">
			<?php 
		    if( $trainers_posts ):
				
				if( $section_title ){ 
					echo '<h2 class="main-title">';
					    echo esc_html( $section_title ); 
					echo '</h2>';
				} 
	         
				$trainer_qry = new WP_Query(array(
				    	 'post__in'   => $trainers_posts,
		                 'orderby'    => 'post__in',
		                 'posts_per_page' => -1,
		                 'ignore_sticky_posts' => true
				       ));

				if( $trainer_qry->have_posts() ){ ?>
					<div class="row">
					    <?php 
					    while( $trainer_qry->have_posts() ){
					    	$trainer_qry->the_post(); ?>
						    
						    <div class="col">
						      
						       <?php 
						       if( has_post_thumbnail() ){ ?>
							       <div class="img-holder">
							          <?php the_post_thumbnail( 'pranayama-yoga-home-trainer-thumb' ); ?>
							       </div>
					           <?php 
					           } ?>

								<div class="text-holder">
									<h3 class="name"><?php the_title(); ?></h3>
									<?php
									if( has_excerpt() ){ ?>
									    <span class ="designation">
									        <?php the_excerpt(); ?>
									    </span>
								    <?php } 
								     the_content(); ?>
								</div>
							</div>
					    <?php 
					    } ?>
				    </div>
			    <?php
			    } 
			    wp_reset_postdata();
			endif; ?>
		</div>
	</section>