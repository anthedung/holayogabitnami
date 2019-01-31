<?php
/**
* The template for displaying the footer
*
* @package kale
*/
?>

        <?php if(is_front_page() && !is_paged() ) { 
            get_template_part('parts/frontpage', 'large'); 
        } ?>

        <?php get_sidebar('footer'); ?>
        
        <!-- Footer -->
        <div class="footer">
            
            <?php if ( is_active_sidebar( 'footer-row-3-center' ) ) { ?>
            <div class="footer-row-3-center"><?php dynamic_sidebar( 'footer-row-3-center' ); ?>
            <?php } ?>
            
            <?php $kale_footer_copyright = kale_get_option('kale_footer_copyright'); ?>
            <?php if($kale_footer_copyright) { ?>
            <div class="footer-copyright"><?php echo wp_kses_post($kale_footer_copyright); ?></div>
            <?php } ?>
            
<!--             <div class="footer-copyright">
                <ul class="credit">
                    
                </ul>
            </div> -->
            
        </div>
        <!-- /Footer -->
        
    </div><!-- /Container -->
</div><!-- /Main Wrapper -->

<?php wp_footer(); ?>
</body>
</html>
