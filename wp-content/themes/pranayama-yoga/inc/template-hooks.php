<?php 
 /**
     * Doctype Hook
     * 
     * @see pranayama_yoga_doctype_cb
    */
add_action( 'pranayama_yoga_doctype', 'pranayama_yoga_doctype_cb');

 /**
     * Before wp_head
     * 
     * @see pranayama_yoga_head
    */

add_action( 'pranayama_yoga_before_wp_head', 'pranayama_yoga_head');

 /**
     * Page Start
     * 
     * @see pranayama_yoga_page_start
    */

add_action( 'pranayama_yoga_before_page_start','pranayama_yoga_page_start');

 /**
     * Rara Academic Header
     * 
     * @see pranayama_yoga_header_start - 10
     * @see pranayama_yoga_header_top - 20
     * @see pranayama_yoga_header_bottom - 30
     * @see pranayama_yoga_header_end - 40
     *
    */
add_action( 'pranayama_yoga_header', 'pranayama_yoga_header_start', 10 );
add_action( 'pranayama_yoga_header', 'pranayama_yoga_header_top', 20 );
add_action( 'pranayama_yoga_header', 'pranayama_yoga_header_bottom', 30 );
add_action( 'pranayama_yoga_header', 'pranayama_yoga_header_end', 40 );

 /**
     * Page Header
     * 
     * @see pranayama_yoga_page_header - 10
    */

add_action( 'pranayama_yoga_after_header', 'pranayama_yoga_page_header', 10 );

 /**
     * Breadcrumbs
     * 
     * @see pranayama_yoga_breadcrumbs_cb 
    */

add_action( 'pranayama_yoga_breadcrumbs', 'pranayama_yoga_breadcrumbs_cb' );

/** Content HOOKS goes here */

/**
 * Before Page entry content
 * 
 * @see pranayama_yoga_page_content_image
*/
add_action( 'pranayama_yoga_before_page_entry_content', 'pranayama_yoga_page_content_image' );

/**
 * Before Post entry content
 * 
 * @see pranayama_yoga_post_content_image - 10
 * @see pranayama_yoga_post_entry_header  - 20
*/
add_action( 'pranayama_yoga_before_post_entry_content', 'pranayama_yoga_post_content_image', 10 );
add_action( 'pranayama_yoga_before_post_entry_content', 'pranayama_yoga_post_entry_header', 20 );

/**
 * After post content
 * 
 * @see pranayama_yoga_post_author - 20
*/
add_action( 'pranayama_yoga_after_post_content', 'pranayama_yoga_post_author', 20 );

/**
 * Comment
 * 
 * @see pranayama_yoga_get_comment_section 
*/
add_action( 'pranayama_yoga_comment', 'pranayama_yoga_get_comment_section' );

/**
    * Content End
    * 
    * @see pranayama_yoga_content_end -20
*/

add_action( 'pranayama_yoga_after_content', 'pranayama_yoga_content_end', 20 );

 /**
     * Rara Academic Footer
     * 
     * @see pranayama_yoga_footer_start - 10
     * @see pranayama_yoga_footer_top - 20
     * @see pranayama_yoga_footer_bottom - 30
     * @see pranayama_yoga_footer_end - 40
    */

add_action( 'pranayama_yoga_footer', 'pranayama_yoga_footer_start', 10 );
add_action( 'pranayama_yoga_footer', 'pranayama_yoga_footer_top', 20 );
add_action( 'pranayama_yoga_footer', 'pranayama_yoga_footer_bottom', 30 );
add_action( 'pranayama_yoga_footer', 'pranayama_yoga_footer_end', 40 );

 /**
     * page start
     * 
     * @see pranayama_yoga_page_end - 20
    */

add_action( 'pranayama_yoga_after_footer', 'pranayama_yoga_page_end', 20 );