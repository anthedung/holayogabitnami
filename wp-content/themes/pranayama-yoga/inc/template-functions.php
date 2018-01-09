<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package pranayama_yoga
 */


if( ! function_exists( 'pranayama_yoga_doctype_cb' ) ) :
/**
 * Doctype Declaration
 * 
 * @since 1.0.1
*/
function pranayama_yoga_doctype_cb(){
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>
    <?php
}
endif;



if( ! function_exists( 'pranayama_yoga_head' ) ) :
/**
 * Before wp_head
 * 
 * @since 1.0.1
*/
function pranayama_yoga_head(){
    ?>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php
}
endif;


if( ! function_exists( 'pranayama_yoga_page_start' ) ) :
/**
 * Page Start
 * 
 * @since 1.0.1
*/
function pranayama_yoga_page_start(){
    ?>
    <div id="page" class="site">
    <?php
}
endif;


if( ! function_exists( 'pranayama_yoga_header_start' ) ) :
/**
 * Header Start
 * 
 * @since 1.0.1
*/
function pranayama_yoga_header_start(){
    ?>
    <header id="masthead" class="site-header" role="banner">
       
    <?php 
}
endif;


if( ! function_exists( 'pranayama_yoga_header_top' ) ) :
/**
 * Header Top
 * 
 * @since 1.0.1
*/
    function pranayama_yoga_header_top(){

        $ed_social     = get_theme_mod('pranayama_yoga_ed_social');
        $address       = get_theme_mod('pranayama_yoga_address');
        $phone_text    = get_theme_mod('pranayama_yoga_text');
        $phone_number  = get_theme_mod('pranayama_yoga_phone');

        if( $ed_social || $address || $phone_text || $phone_number ){
    ?>
            <div class="header-t">
                <div class="container">      
                    
                    <?php 
                    if( $address ){ ?>
                        <div class="contact-info">
                            <span class="fa fa-map-marker"></span>
                            <?php echo wp_kses_post( $address ); ?>
                        </div>
                    <?php 
                    } ?>

                    <div class="right-panel">
                        <?php 
                        if( $phone_number ){ ?>
                            <div class="contact-number">
                                <span><?php echo esc_html( $phone_text ); ?></span>
                                <a href="tel:<?php echo preg_replace('/\D/', '', $phone_number ); ?>"><?php echo esc_html( $phone_number ); ?></a>
                            </div>
                        <?php 
                        } 
                        /**
                         * Social Links
                         * 
                         * @hooked 
                        */
                        if( $ed_social ) pranayama_yoga_social_cb(); ?>
                    </div>
                </div>
            </div>
        <?php 
        }
    }

endif;


if( !function_exists( 'pranayama_yoga_header_bottom' )):
/**
 * Header Bottom
 * 
 * @since 1.0.1
*/
   function pranayama_yoga_header_bottom(){ ?>

        <div class="header-b">
            <div class="container"> 
                
                <div class="site-branding">
                    
                    <?php 
                        if( function_exists( 'has_custom_logo' ) && has_custom_logo() ){
                            the_custom_logo();
                        } 
                    ?>
                        <div class="text-logo">
                            <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
                            </h1>

                            <?php
                            $description = get_bloginfo( 'description', 'display' );
                            if ( $description || is_customize_preview() ) : ?>
                                <p class="site-description"><?php echo esc_html( $description ); /* WPCS: xss ok. */ ?></p>
                             <?php   
                            endif; ?>
                        </div>

                </div><!-- .site-branding -->
                <div class="right-panel">
                    
                    <div class="btn-search">
                        <span class="search fa fa-search">
                        </span>
                        <?php get_search_form(); ?>
                    </div>
                    
                    <div id="mobile-header">
                        <a id="responsive-menu-button" href="#sidr-main">
                            <span></span>
                            <span></span>
                            <span></span>
                        </a>
                    </div>
            
                    <nav id="site-navigation" class="main-navigation" role="navigation">
                        
                        <?php wp_nav_menu( array( 
                                'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
                    </nav><!-- #site-navigation -->
                </div>
            </div>
        </div>

<?php 
   
   } 

endif;



if( ! function_exists( 'pranayama_yoga_header_end' ) ) :
/**
 * Header End
 * 
 * @since 1.0.1
*/
    function pranayama_yoga_header_end(){
        ?>
        </header>
        <?php
    }
endif;

if( !function_exists( 'pranayama_yoga_breadcrumbs_cb' ) ):
/**
 * Breadcrumb
*/
  function pranayama_yoga_breadcrumbs_cb(){
    
      $showOnHome    = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
      $delimiter     = esc_html( get_theme_mod( 'pranayama_yoga_breadcrumb_separator', __( '>', 'pranayama-yoga' ) ) ); // delimiter between crumbs
      $home          = esc_html( get_theme_mod( 'pranayama_yoga_breadcrumb_home_text', __( 'Home', 'pranayama-yoga' ) ) ); // text for the 'Home' link
      $showCurrent   = get_theme_mod( 'pranayama_yoga_ed_current', '1' ); // 1 - show current post/page title in breadcrumbs, 0 - don't show
      $before        = '<span class="current">'; // tag before the current crumb
      $after         = '</span>'; // tag after the current crumb
      $before_text   = esc_html__( 'You are here: ','pranayama-yoga' );
      $ed_breadcrumb = get_theme_mod( 'pranayama_yoga_ed_breadcrumb' );
      
      global $post;
      $homeLink = esc_url( home_url( ) );
      
      if( $ed_breadcrumb ){
          if ( is_front_page() ) {
          
              if ( $showOnHome == 1 ) echo '<div id="crumbs"><a href="' . esc_url( $homeLink ) . '">' . esc_html( $home ) . '</a></div>';
          
          } else {
          
              echo '<div id="crumbs">' . $before_text . '<a href="' . esc_url( $homeLink ) . '">' . esc_html( $home ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
          
              if ( is_category() ) {
                  $thisCat = get_category( get_query_var( 'cat' ), false );
                  if ( $thisCat->parent != 0 ) echo get_category_parents( $thisCat->parent, TRUE, ' <span class="separator">' . $delimiter . '</span> ' );
                  echo $before .  esc_html( single_cat_title( '', false ) ) . $after;
              
              } elseif ( is_search() ) {
                  echo $before . esc_html__( 'Search Results for "', 'pranayama-yoga' ) . esc_html( get_search_query() ) . esc_html__( '"', 'pranayama-yoga' ) . $after;
              
              } elseif ( is_day() ) {
                  echo '<a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '">' . esc_html( get_the_time( 'Y' ) ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                  echo '<a href="' . esc_url( get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) ) . '">' . esc_html( get_the_time( 'F' ) ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                  echo $before . esc_html( get_the_time( 'd' ) ) . $after;
              
              } elseif ( is_month() ) {
                  echo '<a href="' . esc_url( get_year_link( get_the_time( 'Y' ) ) ) . '">' . esc_html( get_the_time( 'Y' ) ) . '</a> <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                  echo $before . esc_html( get_the_time( 'F' ) ) . $after;
              
              } elseif ( is_year() ) {
                  echo $before . esc_html( get_the_time( 'Y' ) ) . $after;
          
              } elseif ( is_single() && !is_attachment() ) {
                  if ( get_post_type() != 'post' ) {
                      $post_type = get_post_type_object(get_post_type());
                      $slug = $post_type->rewrite;
                      echo '<a href="' . esc_url( $homeLink . '/' . $slug['slug'] ) . '/">' . esc_html( $post_type->labels->singular_name ) . '</a>';
                      if ( $showCurrent == 1 ) echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ' . $before . esc_html( get_the_title() ) . $after;
                  } else {
                      $cat = get_the_category(); 
                      if( $cat ){
                          $cat = $cat[0];
                          $cats = get_category_parents( $cat, TRUE, ' <span class="separator">' . esc_html( $delimiter ) . '</span> ' );
                          if ( $showCurrent == 0 ) $cats = preg_replace( "#^(.+)\s$delimiter\s$#", "$1", $cats );
                          echo $cats;
                      }
                      if ( $showCurrent == 1 ) echo $before . esc_html( get_the_title() ) . $after;
                  }
              
              } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
                  $post_type = get_post_type_object(get_post_type());
                  echo $before . esc_html( $post_type->labels->singular_name ) . $after;
              
              } elseif ( is_attachment() ) {
                  $parent = get_post( $post->post_parent );
                  $cat = get_the_category( $parent->ID ); 
                  if( $cat ){
                      $cat = $cat[0];
                      echo get_category_parents( $cat, TRUE, ' <span class="separator">' . esc_html( $delimiter ) . '</span> ');
                      echo '<a href="' . esc_url( get_permalink( $parent ) ) . '">' . esc_html( $parent->post_title ) . '</a>' . ' <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                  }
                  if ( $showCurrent == 1 ) echo  $before . esc_html( get_the_title() ) . $after;
              
              } elseif ( is_page() && !$post->post_parent ) {
                  if ( $showCurrent == 1 ) echo $before . esc_html( get_the_title() ) . $after;
          
              } elseif ( is_page() && $post->post_parent ) {
                  $parent_id  = $post->post_parent;
                  $breadcrumbs = array();
                  while( $parent_id ){
                      $page = get_post( $parent_id );
                      $breadcrumbs[] = '<a href="' . esc_url( get_permalink( $page->ID ) ) . '">' . esc_html( get_the_title( $page->ID ) ) . '</a>';
                      $parent_id  = $page->post_parent;
                  }
                  $breadcrumbs = array_reverse( $breadcrumbs );
                  for ( $i = 0; $i < count( $breadcrumbs) ; $i++ ){
                      echo $breadcrumbs[$i];
                      if ( $i != count( $breadcrumbs ) - 1 ) echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ';
                  }
                  if ( $showCurrent == 1 ) echo ' <span class="separator">' . esc_html( $delimiter ) . '</span> ' . $before . esc_html( get_the_title() ) . $after;
              
              } elseif ( is_tag() ) {
                  echo $before . esc_html( single_tag_title( '', false ) ) . $after;
              
              } elseif ( is_author() ) {
                  global $author;
                  $userdata = get_userdata( $author );
                  echo $before . esc_html( $userdata->display_name ) . $after;
              
              } elseif ( is_404() ) {
                  echo $before . esc_html__( '404 Error - Page not Found', 'pranayama-yoga' ) . $after;
              } elseif( is_home() ){
                  echo $before;
                  single_post_title();
                  echo $after;
              }
          
              echo '</div>';   
          }
      }
      
  } // end pranayama_yoga_breadcrumbs()
endif;


if( ! function_exists( 'pranayama_yoga_page_header' ) ):
/**
 * Page Header 
*/
    function pranayama_yoga_page_header(){
        
        if( ! is_page_template( 'template-home.php' ) ){ ?>
            <div class="top-bar">
                <div class="container">
                    <div class="page-header">
                        <h1 class="page-title">
                            <?php 
                                if( is_page()){
                                    the_title();
                                }
                                  
                                if(is_search()){ 
                                    printf( esc_html__( 'Search Results for: %s', 'pranayama-yoga' ), '<span>' . get_search_query() . '</span>' );
                                }
                                
                                if(is_archive()){
                                    the_archive_title();
                                }
                                
                                if(is_404()) {
                                    printf( esc_html__( '404 - Page not found', 'pranayama-yoga' )); 
                                }

                                if ( is_home() && ! is_front_page() ){
                                    single_post_title();
                                }
                            ?>
                        </h1>
                    </div>
                    <?php do_action( 'pranayama_yoga_breadcrumbs' ); ?>  
                </div>
            </div>
            <div id="content" class="site-content">
                <div class="container">
                    <div class="row">
        <?php   
        }
    }
endif;



if( ! function_exists( 'pranayama_yoga_page_content_image' ) ) :
/**
 * Page Featured Image
*/
    function pranayama_yoga_page_content_image(){
   
        $sidebar_layout = pranayama_yoga_sidebar_layout();
        
        if( has_post_thumbnail() ){
            echo '<div class="post-thumbnail">';
            ( is_active_sidebar( 'right-sidebar' ) && ( $sidebar_layout == 'right-sidebar' ) ) ? the_post_thumbnail( 'pranayama-yoga-blog-thumb' ) : the_post_thumbnail( 'pranayama-yoga-blog-full-width-thumb' );    
            echo '</div>';
    }

}
endif;

if( ! function_exists( 'pranayama_yoga_post_content_image' ) ) :
/**
 * Post Featured Image
*/
    function pranayama_yoga_post_content_image(){
        if( has_post_thumbnail() ){ 
            echo is_single() ? '<div class="post-thumbnail">' : '<a href="' . esc_url( get_permalink() ) . '" class="post-thumbnail">';    
            
            is_active_sidebar( 'right-sidebar' ) ? the_post_thumbnail( 'pranayama-yoga-blog-thumb' ) : the_post_thumbnail( 'pranayama-yoga-blog-full-width-thumb' );    
            
            echo is_single() ? '</div>' : '</a>';
        }        
    }
endif;

if( ! function_exists( 'pranayama_yoga_post_entry_header' ) ) :
/**
 * Post Entry Header
*/
    function pranayama_yoga_post_entry_header(){
        ?>
        <header class="entry-header">
            <?php
                if( is_single() ){
                    the_title( '<h1 class="entry-title">', '</h1>' );
                }else{
                    the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                }
            ?>
            <div class="entry-meta">
                <?php 
                if ( 'post' === get_post_type() ){ 
                    pranayama_yoga_get_post_meta(); 
                } 
                ?>
            </div>
        </header><!-- .entry-header -->
        <?php
    }
endif;

if( ! function_exists( 'pranayama_yoga_post_author' ) ) :
/**
 * Author Bio
 * 
*/
    function pranayama_yoga_post_author(){
        if( get_the_author_meta( 'description' ) ){
        ?>
        <section class="author">
            <div class="img-holder">
                <?php echo get_avatar( get_the_author_meta( 'ID' ), 105 ); ?>
            </div>
            <div class="text-holder">
                <span class="name"><?php printf( esc_html__( 'About %s', 'pranayama-yoga' ), esc_html( get_the_author_meta( 'display_name' ) ) ); ?></span>              
                <?php echo wpautop( wp_kses_post ( get_the_author_meta( 'description' ) ) ); ?>
            </div>
        </section>
        <?php  
        }  
    }
endif;


if( ! function_exists( 'pranayama_yoga_get_comment_section' ) ) :
/**
 * Comment template
 * 
*/
    function pranayama_yoga_get_comment_section(){
        // If comments are open or we have at least one comment, load up the comment template.
        if ( comments_open() || get_comments_number() ) :
            comments_template();
        endif;
    }
endif;
if( ! function_exists( 'pranayama_yoga_content_end' ) ) :
/**
 * Content End
 * 
 * @since 1.0.1
*/
    function pranayama_yoga_content_end(){
        if(! is_page_template('template-home.php')){
        ?>
                    </div><!-- row -->
                </div><!-- .content -->
            </div><!-- #container -->
            
        <?php
        }
    }
    endif;



if( ! function_exists( 'pranayama_yoga_footer_start' ) ) :
/**
 * Footer Start
 * 
 * @since 1.0.1
*/
    function pranayama_yoga_footer_start(){
        ?>
        <footer id="colophon" class="site-footer" role="contentinfo">
            <div class="container">
        <?php
    }
endif;



if( ! function_exists( 'pranayama_yoga_footer_top' ) ) :
/**
 * Footer Top
 * 
 * @since 1.0.1
*/
    function pranayama_yoga_footer_top(){    
        if( is_active_sidebar( 'footer-one' ) || is_active_sidebar( 'footer-two' ) || is_active_sidebar( 'footer-three' ) || is_active_sidebar( 'footer-four' ) ){
        ?>
           <div class="footer-t">
                <div class="row">
                    
                    <?php if( is_active_sidebar( 'footer-one' ) ){ ?>
                        <div class="column">
                           <?php dynamic_sidebar( 'footer-one' ); ?>    
                        </div>
                    <?php } ?>
                    
                    <?php if( is_active_sidebar( 'footer-two' ) ){ ?>
                        <div class="column">
                           <?php dynamic_sidebar( 'footer-two' ); ?>    
                        </div>
                    <?php } ?>
                    
                    <?php if( is_active_sidebar( 'footer-three' ) ){ ?>
                        <div class="column">
                           <?php dynamic_sidebar( 'footer-three' ); ?>  
                        </div>
                    <?php } ?>

                    <?php if( is_active_sidebar( 'footer-four' ) ){ ?>
                        <div class="column">
                           <?php dynamic_sidebar( 'footer-four' ); ?>   
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php 
        }   
    }
endif;



if( ! function_exists( 'pranayama_yoga_footer_bottom' ) ) :
/**
 * Footer Bottom
 * 
 * @since 1.0.1 
*/
    function pranayama_yoga_footer_bottom(){
      $copyright = get_theme_mod( 'pranayama_yoga_footer_copyright_text' );
    ?>  
        <div class="footer-b">
            
            <div class="site-info">
              <?php if( $copyright ){
                echo wp_kses_post( $copyright );
              } else { ?>
                <?php echo esc_html__( '&copy; Copyright ', 'pranayama-yoga' ) . date('Y'); ?> 
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a>.
                    
                <a href="<?php echo esc_url( 'http://raratheme.com/wordpress-themes/pranayama-yoga/' ); ?>" rel="author" target="_blank"><?php echo esc_html__( 'Pranayama Yoga by Rara Theme.', 'pranayama-yoga' ); ?></a>
                
                <?php 
                printf( esc_html__( 'Powered by %s', 'pranayama-yoga' ), '<a href="'. esc_url( __( 'https://wordpress.org/', 'pranayama-yoga' ) ) .'" target="_blank">WordPress.</a>' );
                }
              ?>
            </div>
            
            <?php 
                $social_footer = get_theme_mod('pranayama_yoga_ed_social_footer'); 
                
                if( $social_footer) pranayama_yoga_social_cb(); ?> 
        </div>
    <?php }
endif;



if( ! function_exists( 'pranayama_yoga_footer_end' ) ) :
/**
 * Footer End
 * 
 * @since 1.0.1 
*/
    function pranayama_yoga_footer_end(){
        ?>
        </div>
        </footer><!-- #colophon -->
        <?php
    }
endif;

if( ! function_exists( 'pranayama_yoga_page_end' ) ) :
/**
 * Page End
 * 
 * @since 1.0.1
*/
    function pranayama_yoga_page_end(){
        ?>
        </div><!-- #page -->
        <?php
    }
endif;
