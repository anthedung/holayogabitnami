<?php
/**
* Yoga Classes Section
*
* @package pranayama_yoga
*/ 
    $class_title = get_theme_mod('pranayama_yoga_classes_section_title');
    $post_one    = get_theme_mod( 'pranayama_yoga_class_post_one' );
    $post_two    = get_theme_mod( 'pranayama_yoga_class_post_two' );
    $post_three  = get_theme_mod( 'pranayama_yoga_class_post_three' );
    $post_four   = get_theme_mod( 'pranayama_yoga_class_post_four' );
    $post_five   = get_theme_mod( 'pranayama_yoga_class_post_five' );
    $post_six    = get_theme_mod( 'pranayama_yoga_class_post_six' );
    
    $posts = array( $post_one, $post_two, $post_three, $post_four, $post_five, $post_six );
    $posts = array_diff( array_unique( $posts ), array('') );
    ?>
    <section class="section-three">
        <?php 
	    if( $posts ): 
		    $class_qry = new WP_Query( array(
		            'post__in'   => $posts,
		            'orderby'   => 'post__in',
		            'posts_per_page' => '6',
		            'ignore_sticky_posts' => true
		        ));
		    ?>   
		    <div class="container">

		        <?php 
		        if( $class_title ){  ?>
					<h2 class="main-title"><?php echo esc_html( $class_title ); ?></h2>
				<?php 
			    }

			    if( $class_qry->have_posts() ){ ?>
					<div class="row">   
					    
					    <?php 
					    while( $class_qry->have_posts() ){
					    	$class_qry->the_post();
					    ?>
						
							<div class="col">
							    <?php 
							    if(has_post_thumbnail()){ ?>
								    <a href="<?php the_permalink() ?>" class="img-holder">
								        <?php 
								        the_post_thumbnail('pranayama-yoga-home-classes-thumb');
								        ?>
								    </a>
							    <?php 
							    } 
							    ?>
								<div class="text-holder">
									<h3 class="title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
									<?php 
									if( has_excerpt() ){ ?>
									    <div class="time">
									        <?php the_excerpt(); ?>
									    </div>
								    <?php } ?>
								</div>
							</div>
					    
					    <?php 
					    } ?>
					
					</div>  
				<?php 
				}
				wp_reset_postdata();   ?>
		    </div>
		<?php 
		endif; ?>
	</section>