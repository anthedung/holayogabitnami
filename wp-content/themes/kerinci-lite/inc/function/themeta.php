<?php

// akmanda category plug and play
function kerinci_lite_category(){
    echo '<div class="category-wrapper">';
    echo '<span>Category :</span> ';
    the_category(', ');
    echo '</div>';

}

// akmanda author plug and play
function kerinci_lite_author(){
    global $post;
    echo '<div class="author-wrapper">';
    echo '<p><span class="author-icon">'; ?><?php esc_html_e( 'by', 'kerinci-lite' ); ?></span>
    <span itemprop="author" itemscope="itemscope" itemtype="http://schema.org/Person">
    <span class="author vcard" itemprop="name">
    <?php echo get_the_author_meta( 'display_name' );
    echo '</span>
    </span></p>
    </div>';
}

// akmanda date plug and play
function kerinci_lite_date(){
    global $post;
    echo '<span class="date-post">'; ?>
    <time class="entry-date" datetime="<?php the_modified_date('Y-m-j'); ?>" itemprop="dateModified"><?php echo get_the_date(); ?></time>
    <meta itemprop="datePublished" content="<?php echo get_the_date(); ?>">
    <?php echo '</span>';
}

// akmanda tags plug and play
function kerinci_lite_tags(){
    global $post;
    echo '<div class="tag-wrapper">';
    echo '<span>Tags :</span> ';
    the_tags('',', ','');
    echo '</div>';
}


function kerinci_lite_comments(){
    global $post;

    comments_number( '0 Comments', '1 Comments', '% Comments' );

}

function kerinci_lite_post_type() {


if ( !get_post_format() ) {
    echo '<i class="icon post-type icon-elusive-icons-1"></i>';
}


}

function kerinci_lite_meta_info(){ ?>

    <div class="post-meta clearfix"> 
         <?php the_category(', '); ?> 
        <?php kerinci_lite_date(); ?>
    </div>         
<?php } 


