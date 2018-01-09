 <?php
/**
* Reason Section
*
* @package pranayama_yoga
*/  
    $reason_page = get_theme_mod( 'pranayama_yoga_reason_section_page' );
    $post_one    = get_theme_mod( 'pranayama_yoga_reason_post_one' );
    $post_two    = get_theme_mod( 'pranayama_yoga_reason_post_two' );
    $post_three  = get_theme_mod( 'pranayama_yoga_reason_post_three' );
    $post_four   = get_theme_mod( 'pranayama_yoga_reason_post_four' );
    $post_five   = get_theme_mod( 'pranayama_yoga_reason_post_five' );
    $post_six    = get_theme_mod( 'pranayama_yoga_reason_post_six' );

    $posts_left  = array( $post_one, $post_three, $post_five );
    $posts_left  = array_diff( array_unique( $posts_left ), array('') );
    
    $posts_right = array( $post_two, $post_four, $post_six );
    $posts_right = array_diff( array_unique( $posts_right ), array('') );
?>  
    <div class="section-eight">
	    <div class="container">
		    <?php 
            if( $reason_page ): 
            	$qry = new WP_Query( array( 'post_type'=> 'page', 'p'=> $reason_page ));

                if( $qry->have_posts() ):
                	while( $qry->have_posts() ): $qry->the_post();
			            echo '<h2 class="main-title">';
			                the_title(); 
			            echo '</h2>';
			        endwhile;
			    endif;
			    wp_reset_postdata();
			endif;

			echo '<div class="row">';
                
                /*
                left Col
                */
			    if( $posts_left ): 
				    $left_qry = new WP_Query( array(
				            'post__in'   => $posts_left,
				            'orderby'   => 'post__in',
				            'posts_per_page' => -1,
				            'ignore_sticky_posts' => true

				        ));

					if( $left_qry->have_posts() ){  ?>	
						<div class="col-left">  
							<?php 
							while( $left_qry->have_posts() ): 
							    $left_qry->the_post();
							?>
							    <div class="holder">
								    <?php 
									if(has_post_thumbnail()){ ?>
										<div class="icon-holder"> 
										    <?php the_post_thumbnail( 'pranayama-yoga-reason-thumb' ); ?>
									    </div>
									<?php } ?>

									<div class="text-holder">
										<h3 class="title"><?php the_title(); ?></h3>
										<?php the_content(); ?>
								    </div>
								</div>
		                    <?php 
		                    endwhile;
		                    ?>
						</div>
					<?php   
		            } wp_reset_postdata();
	            endif; 
                
                /*
                middle Col
                */
	            if( $reason_page ): 
            	$qry = new WP_Query( array( 'post_type'=> 'page', 'p'=> $reason_page ));

	                if( $qry->have_posts() ):
	                	while( $qry->have_posts() ): $qry->the_post();

	                        if( has_post_thumbnail() ): ?>
		                        <div class="col-mid">
							        <div class="img-holder"><?php the_post_thumbnail(); ?></div>
						        </div>
						    <?php 
						    endif;
				            
				        endwhile;
				    endif;
				    wp_reset_postdata();
			    endif;
                
                /*
                right Col
                */
				if( $posts_right ) :  
					
					$right_qry = new WP_Query( array(
				            'post__in'   => $posts_right,
				            'orderby'   => 'post__in',
				            'posts_per_page' => -1,
				            'ignore_sticky_posts' => true,
				    ));

					if( $right_qry->have_posts() ){  ?>	
						<div class="col-right">  
							<?php 
							while( $right_qry->have_posts() ): 
							    $right_qry->the_post();
							?>
							    <div class="holder">
								    <?php 
									if(has_post_thumbnail()){ ?>
										<div class="icon-holder"> 
										    <?php the_post_thumbnail( 'pranayama-yoga-reason-thumb' ); ?>
									    </div>
									<?php } ?>

									<div class="text-holder">
										<h3 class="title"><?php the_title(); ?></h3>
										<?php the_content(); ?>
								    </div>
								</div>
		                    <?php 
		                    endwhile;
		                    ?>
						</div>
					<?php   
		            } wp_reset_postdata(); 
		        endif;
		    echo '</div>'; ?>
	    </div>
	</div>