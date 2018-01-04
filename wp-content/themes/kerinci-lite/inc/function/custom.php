<?php
function kerinci_lite_social_profile() {

if ( current_user_can( 'edit_theme_options' ) ) {

  $kerinci_lite_twitter_url   = get_theme_mod( 'kerinci_lite_twitter_url', esc_url( '#' ) );
  $kerinci_lite_googlep_url   = get_theme_mod( 'kerinci_lite_googlep_url', esc_url( '#' ) );
  $kerinci_lite_facebook_url  = get_theme_mod( 'kerinci_lite_facebook_url', esc_url( '#' ) );
  $kerinci_lite_linkedin_url  = get_theme_mod( 'kerinci_lite_linkedin_url', esc_url( '#' ) );
  $kerinci_lite_pinterest_url = get_theme_mod( 'kerinci_lite_pinterest_url', esc_url( '#' ) );
} else {
  $kerinci_lite_twitter_url   = get_theme_mod( 'kerinci_lite_twitter_url' );
  $kerinci_lite_googlep_url   = get_theme_mod( 'kerinci_lite_googlep_url' );
  $kerinci_lite_facebook_url  = get_theme_mod( 'kerinci_lite_facebook_url' );
  $kerinci_lite_linkedin_url  = get_theme_mod( 'kerinci_lite_linkedin_url' );
  $kerinci_lite_pinterest_url = get_theme_mod( 'kerinci_lite_pinterest_url' );
}

if (!empty($kerinci_lite_twitter_url)) { ?>
    <li class="twitter"><a title="Twitter" href="<?php echo esc_url( $kerinci_lite_twitter_url ); ?>" class="icon icon-twitter-alt"></a></li>
<?php } 

if (!empty($kerinci_lite_googlep_url)) { ?>
  <li class="google"><a title="Googlep" href="<?php echo esc_url( $kerinci_lite_googlep_url ); ?>" class="icon icon-google"></a></li>
<?php } 

if (!empty($kerinci_lite_facebook_url)) { ?>
  <li class="facebook"><a title="Facebook" href="<?php echo esc_url( $kerinci_lite_facebook_url ); ?>" class="icon icon-facebook-1"></a></li>
<?php } 

if (!empty($kerinci_lite_linkedin_url)) { ?>
  <li class="linkedin"><a title="LinkedIn" href="<?php echo esc_url( $kerinci_lite_linkedin_url ); ?>" class="icon icon-linkedin"></a></li>
<?php } 

if (!empty($kerinci_lite_pinterest_url)) { ?>
  <li class="pinterest"><a title="Pinterest" href="<?php echo esc_url( $kerinci_lite_pinterest_url ); ?>" class="icon icon-path"></a></li>
<?php } 

 }