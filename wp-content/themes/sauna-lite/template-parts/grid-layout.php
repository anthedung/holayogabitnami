<?php
/**
 * The template part for displaying slider
 *
 * @package Sauna Lite 
 * @subpackage sauna_lite
 * @since Sauna Lite 1.0
 */
?>
<div class="col-md-3">
  <div id="post-<?php the_ID(); ?>" <?php post_class('inner-service'); ?>>
    <div class="page-box">
      <div class="box-image">
        <?php 
          if(has_post_thumbnail()) { 
            the_post_thumbnail(); 
          }
        ?>
      </div>
      <div class="box-content">
        <h4><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
        <?php the_excerpt();?>
        <a class="r_button hvr-sweep-to-right read-more"  href="<?php echo esc_url( get_permalink() ); ?>" title="<?php esc_attr_e('READ More&hellip;','sauna-lite'); ?>"><?php esc_html('READ More&hellip;','sauna-lite'); ?></a>
      </div>
      <div class="clearfix"></div>      
    </div>
  </div>
</div>