<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Sauna Lite
 */
?>
    <div id="sauna-footer">
    	<div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-3">
                    <?php dynamic_sidebar('footer-1');?>
                </div>
                <div class="col-md-3 col-sm-3">
                    <?php dynamic_sidebar('footer-2');?>
                </div>
                <div class="col-md-3 col-sm-3">
                    <?php dynamic_sidebar('footer-3');?>
                </div>
                <div class="col-md-3 col-sm-3">
                    <?php dynamic_sidebar('footer-4');?>
                </div>
            </div>
        </div>
    </div>
	<div class="clearfix"></div> 
    <div class="copyright">
        <p><?php echo esc_html(sauna_lite_credit1(),'sauna-lite'); ?> <?php echo esc_html(get_theme_mod('sauna_lite_footer_copy',__('By','sauna-lite'))); ?> <?php echo esc_html(sauna_lite_credit(),'sauna-lite'); ?></p>
    </div>
    <div class="clear"></div>
    <?php wp_footer(); ?>
  </body>
</html>