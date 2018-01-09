<?php 
 /**
* Blog Section
*
* @package pranayama_yoga
*/
     $section_title       = get_theme_mod( 'pranayama_yoga_blog_section_title' );
     $section_description = get_theme_mod( 'pranayama_yoga_blog_section_description' );
 ?> 
    <div class="blog-section section-seven">
	    <div class="container">
			<?php 
			if( $section_title || $section_description ){ ?>
				<header class="header">
					<?php
					if( $section_title )
					    echo '<h2 class="main-title">';
					       echo esc_html( $section_title  );
					    echo '</h2>';

				    if( $section_description )
					   echo wpautop( wp_kses_post ( $section_description ) ); ?>

				</header>

			<?php
			}

			$blog_qry = new WP_Query(array(
			    	'posts_per_page' => 3,
					'post_type' => 'post',
					'ignore_sticky_posts' => true,
			    ));

			if( $blog_qry->have_posts() ){?>
				<div class="row">
					<?php 
					while( $blog_qry->have_posts() ){
						$blog_qry->the_post();?>
						<div class="post">
						    
						    <?php if( has_post_thumbnail() ){ ?>
						        <a href="<?php the_permalink() ?>" class="img-holder">
						           <?php the_post_thumbnail( 'pranayama-yoga-home-blog-thumb' ); ?>
						        </a>
					        <?php } ?>

							<div class="holder">
								<div class="posted-on">
									<span class="date"><?php echo esc_html( get_the_date( 'd' ) ); ?></span>
									<span class="month"><?php echo esc_html( get_the_date( 'M' ) ); ?></span>
								</div>
								<div class="text-holder">
									<h3 class="entry-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
							        <?php the_excerpt(); ?>
								</div>
							</div>

						</div>
				    <?php 
				    } ?>
				</div>
			<?php 
			} wp_reset_postdata(); ?>
		</div>
	</div>