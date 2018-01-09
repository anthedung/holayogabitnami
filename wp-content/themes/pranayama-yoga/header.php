<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package pranayama_yoga
 **
 *
     * Doctype Hook
     * 
     * @hooked pranayama_yoga_doctype_cb
    */
    do_action( 'pranayama_yoga_doctype' );
?>

<head>

<?php 
    /**
     * Before wp_head
     * 
     * @hooked pranayama_yoga_head
    */
    do_action( 'pranayama_yoga_before_wp_head' );

    wp_head(); 
?>
</head>

<body <?php body_class(); ?>>
		
		 <?php
         /*
         * @hooked pranayama_yoga_page_start 
         */
		 do_action( 'pranayama_yoga_before_page_start' ); 

		 /**
	     * pranayama_yoga Header Top
	     * 
	     * @hooked pranayama_yoga_header_start  - 10
	     * @hooked pranayama_yoga_header_top    - 20
	     * @hooked pranayama_yoga_header_bottom - 30
	     * @hooked pranayama_yoga_header_end    - 40    
	    */	    
		 
		 do_action( 'pranayama_yoga_header' ); 
		 
		 /*
		 **
	     * After Header
	     * 
	     * @hooked pranayama_yoga_page_header 
	     *  
	     */

	     do_action( 'pranayama_yoga_after_header' );
