<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content-aa">
 *
 * @package Sauna Lite
 */

?><!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width">
  <link rel="profile" href="http://gmpg.org/xfn/11">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <div class="toggle"><a class="toggleMenu" href="#"><?php esc_html_e('Menu','sauna-lite'); ?></a></div>

  <?php 
    $slide_one = absint( get_theme_mod( 'sauna_lite_slidersettings-page-1' ) );
    $slide_two = absint( get_theme_mod( 'sauna_lite_slidersettings-page-1' ) );
    $slide_three = absint( get_theme_mod( 'sauna_lite_slidersettings-page-1' ) );
    $slide_four = absint( get_theme_mod( 'sauna_lite_slidersettings-page-1' ) );

    if($slide_one == '' && $slide_two == '' &&  $slide_three == '' &&  $slide_four == ''){
      $header_no_absolute = '';
    }
    else{
      $header_no_absolute = 'yes';
    }
  ?>
  <div id="header" <?php if($header_no_absolute == 'yes'){ echo 'class="header-slider"'; } else{ echo 'class="header-noslider"'; } ?> > 

    <div class="container">

      <div class="logo wow bounceInDown">
          <?php sauna_lite_the_custom_logo(); ?>
          <?php if ( is_front_page() && is_home() ) : ?>
            <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
          <?php else : ?>
            <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
          <?php endif;

          $description = get_bloginfo( 'description', 'display' );
          if ( $description || is_customize_preview() ) : ?>
            <p class="site-description"><?php echo esc_html( $description ); ?></p>
          <?php endif; ?>
      </div><?php /** logo **/ ?>

      <div class="menubox nav">
        <div class="">
		    <?php wp_nav_menu( array('theme_location'  => 'primary') ); ?>
        </div><?php /** nav  **/ ?>
      </div><?php /** menubox **/ ?>
      <div class="clear"></div>

    </div>

  </div><?php /** header **/ ?>