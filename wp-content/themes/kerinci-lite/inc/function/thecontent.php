<?php 


//EXCERPT

function custom_excerpt_length( $length ) {
	return 20;
}
add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

function new_excerpt_more( $more ) {
	return '..';
}
add_filter('excerpt_more', 'new_excerpt_more');

function kerinci_lite_excerpt(){
 global $post;
	?> <div class="akmanda-excerpt entry-content" itemprop="text"> <?php
	the_excerpt(); 
	?><div class="more-button clearfix">
	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" class="more"><?php esc_html_e( 'More', 'kerinci-lite' ); ?></a><?php kerinci_lite_date(); ?></div>
	        </div><?php
}

//POST TITLE

//POST THUMBNAIL

function kerinci_lite_post_thumbnail(){

if ( has_post_thumbnail()) {
	$real_image = get_post_thumbnail_id();
	$image_url = wp_get_attachment_image_src( $real_image, 'full'); 
	$image_meta = wp_get_attachment_metadata( $real_image );
	$image_width = $image_meta["width"];
	$image_height = $image_meta["height"];
?>

	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" >
		<div class="post-thumb">
			<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
				<img class="singleimg" src="<?php echo esc_url($image_url[0]) ?>">
				<meta itemprop="url" content="<?php echo esc_url( $image_url[0] ); ?>">
				<?php echo '<meta itemprop="width" content="'.$image_width.'"><meta itemprop="height" content="'.$image_height.'">'; ?>
			</div>
		</div><!-- post thumb -->
	</a>

	<?php } 
} 

//CONTENT

function kerinci_lite_content() {
	global $post;

	if (is_singular() && $post->post_content !== '') { ?>

		<div class="inner-content entry-content" itemprop="text">
			<?php 
			the_content();
			wp_link_pages();
			?>
		</div>
		 <?php } if (!is_singular()) { 
			kerinci_lite_excerpt();
		} 
}
