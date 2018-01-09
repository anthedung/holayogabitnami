<?php
/**
 * Template Name:Page with Left Sidebar
 */

get_header(); ?>

<?php do_action( 'sauna_lite_pageleft_header' ); ?>

<div class="container">
    <div class="middle-align">       
		<div class="col-md-3 col-sm-3">
			<?php get_sidebar('page'); ?>
		</div>		 
		<div class="col-md-9 col-sm-9" id="content-aa" >
			<?php while ( have_posts() ) : the_post(); ?>
                <h1><?php the_title();?></h1>   
                <?php the_content();
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . __( 'Pages:', 'sauna-lite' ),
                    'after'  => '</div>',
                ) );
                ?>
                <?php
                //If comments are open or we have at least one comment, load up the comment template
                	if ( comments_open() || '0' != get_comments_number() )
                    	comments_template();
            ?>
            <?php endwhile; // end of the loop. ?>
        </div>
        <div class="clear"></div>    
    </div>
</div>

<?php do_action( 'sauna_lite_pageleft_footer' ); ?>

<?php get_footer(); ?>