<article  id="post-<?php the_ID(); ?>" <?php post_class( 'post clearfix'); ?> itemprop="blogPost" itemscope="itemscope" itemtype="http://schema.org/WebPage">
<meta itemscope="" itemprop="mainEntityOfPage" itemtype="https://schema.org/WebPage" itemid="https://google.com/article">

    <div class="post-content clearfix">
        <?php kerinci_lite_post_thumbnail(); ?> 
                  
            <div class="post-entry">                                                             
                
            	<div class="page-title">
					<h1 class="entry-title" itemprop="headline">
						<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
					</h1>
				 </div><!-- page-title -->

                <div class="bord"></div>
                    <?php kerinci_lite_content();
                    wp_link_pages(); ?>
            </div> 
            
    </div><!-- post-content -->

</article><!-- #post-<?php the_ID(); ?> -->