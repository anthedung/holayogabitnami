<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package pranayama_yoga
 */

 /**
     * After Content
     * 
     * @hooked pranayama_yoga_content_end - 20
    */
    do_action( 'pranayama_yoga_after_content' );
    
    /**
     * pranayama_yoga Footer
     * 
     * @hooked pranayama_yoga_footer_start  - 10
     * @hooked pranayama_yoga_footer_top    - 20
     * @hooked pranayama_yoga_footer_bottom - 30
     * @hooked pranayama_yoga_footer_end    - 40
    */
    do_action( 'pranayama_yoga_footer' );
    
    /**
     * After Footer
     * 
     * @hooked pranayama_yoga_page_end - 20
    */
    do_action( 'pranayama_yoga_after_footer' );
   
    wp_footer(); ?>

</body>
</html>