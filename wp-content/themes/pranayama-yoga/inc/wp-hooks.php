<?php
/**
 * WordPress Hooks
 *
 * @package pranayama_yoga
 */

/**
 * @see pranayama_yoga_setup
*/
add_action( 'after_setup_theme', 'pranayama_yoga_setup' );

/**
 * @see pranayama_yoga_content_width
*/
add_action( 'after_setup_theme', 'pranayama_yoga_content_width', 0 );

/**
 * @see pranayama_yoga_template_redirect_content_width
*/
add_action( 'template_redirect', 'pranayama_yoga_template_redirect_content_width' );

/**
 * @see pranayama_yoga_scripts
*/
add_action( 'wp_enqueue_scripts', 'pranayama_yoga_scripts' );

add_action( 'customize_controls_enqueue_scripts', 'pranayama_yoga_admin_scripts' );


/**
 * @see pranayama_yoga_body_classes
*/
add_filter( 'body_class', 'pranayama_yoga_body_classes' );

/**
 * @see pranayama_yoga_category_transient_flusher
*/
add_action( 'edit_category', 'pranayama_yoga_category_transient_flusher' );
add_action( 'save_post',     'pranayama_yoga_category_transient_flusher' );

/**
 * Move comment field to the bottm
 * @see pranayama_yoga_move_comment_field_to_bottom
*/
add_filter( 'comment_form_fields', 'pranayama_yoga_move_comment_field_to_bottom' );

/**
 * @see pranayama_yoga_excerpt_more
 * @see pranayama_yoga_excerpt_length
*/
add_filter( 'excerpt_more', 'pranayama_yoga_excerpt_more' );
add_filter( 'excerpt_length', 'pranayama_yoga_excerpt_length', 999 );

/**
 * @see pranayama_yoga_change_comment_form_default_fields
 * @see pranayama_yoga_change_comment_form_defaults
*/
add_filter( 'comment_form_default_fields', 'pranayama_yoga_change_comment_form_default_fields' );
add_filter( 'comment_form_defaults', 'pranayama_yoga_change_comment_form_defaults' );