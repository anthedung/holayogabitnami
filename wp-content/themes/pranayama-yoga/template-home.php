<?php
/**
 * Template Name: Home Page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Pranayama Yoga
 */

 get_header();

    $pranayama_yoga_page_sections = array( 'slider', 'about', 'info', 'yogaclasses', 'promotional', 'trainer', 'testimonials', 'blog', 'reason', 'subscription' );

    foreach( $pranayama_yoga_page_sections as $section ){ 
        if( get_theme_mod( 'pranayama_yoga_ed_' . $section . '_section' ) == 1 ){
            get_template_part( 'sections/' . esc_attr( $section ) );
        } 
    }
    
 get_footer();