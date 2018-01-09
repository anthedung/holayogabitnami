<?php
/**
 * Pranayama Yoga Theme Info
 *
 * @package pranayama_yoga
 */

function pranayama_yoga_customizer_theme_info( $wp_customize ) {
	
    $wp_customize->add_section( 'theme_info' , array(
		'title'       => __( 'Information Links' , 'pranayama-yoga' ),
		'priority'    => 6,
		));

	$wp_customize->add_setting('theme_info_theme',array(
		'default' => '',
		'sanitize_callback' => 'wp_kses_post',
		));
    
    $theme_info = '';
	$theme_info .= '<h3 class="sticky_title">' . __( 'Need help?', 'pranayama-yoga' ) . '</h3>';
    $theme_info .= '<span class="sticky_info_row"><label class="row-element">' . __( 'View demo', 'pranayama-yoga' ) . ': </label><a href="' . esc_url( 'http://raratheme.com/previews/?theme=pranayama-yoga' ) . '" target="_blank">' . __( 'here', 'pranayama-yoga' ) . '</a></span><br />';
	$theme_info .= '<span class="sticky_info_row"><label class="row-element">' . __( 'View documentation', 'pranayama-yoga' ) . ': </label><a href="' . esc_url( 'http://raratheme.com/documentation/pranayama-yoga/' ) . '" target="_blank">' . __( 'here', 'pranayama-yoga' ) . '</a></span><br />';
    $theme_info .= '<span class="sticky_info_row"><label class="row-element">' . __( 'Theme info', 'pranayama-yoga' ) . ': </label><a href="' . esc_url( 'https://raratheme.com/wordpress-themes/pranayama-yoga/' ) . '" target="_blank">' . __( 'here', 'pranayama-yoga' ) . '</a></span><br />';
    $theme_info .= '<span class="sticky_info_row"><label class="row-element">' . __( 'Support ticket', 'pranayama-yoga' ) . ': </label><a href="' . esc_url( 'https://raratheme.com/support-ticket/' ) . '" target="_blank">' . __( 'here', 'pranayama-yoga' ) . '</a></span><br />';
	$theme_info .= '<span class="sticky_info_row"><label class="row-element">' . __( 'Rate this theme', 'pranayama-yoga' ) . ': </label><a href="' . esc_url( 'https://wordpress.org/support/theme/pranayama-yoga/reviews' ) . '" target="_blank">' . __( 'here', 'pranayama-yoga' ) . '</a></span><br />';
	$theme_info .= '<span class="sticky_info_row"><label class="more-detail row-element">' . __( 'More WordPress Themes', 'pranayama-yoga' ) . ': </label><a href="' . esc_url( 'https://raratheme.com/wordpress-themes/' ) . '" target="_blank">' . __( 'here', 'pranayama-yoga' ) . '</a></span><br />';


	$wp_customize->add_control( new pranayama_yoga_Theme_Info( $wp_customize ,'theme_info_theme',array(
		'label' => __( 'About Pranayama Yoga' , 'pranayama-yoga' ),
		'section' => 'theme_info',
		'description' => $theme_info
		)));

	$wp_customize->add_setting('theme_info_more_theme',array(
		'default' => '',
		'sanitize_callback' => 'wp_kses_post',
		));

}
add_action( 'customize_register', 'pranayama_yoga_customizer_theme_info' );


if( class_exists( 'WP_Customize_control' ) ){

	class pranayama_yoga_Theme_Info extends WP_Customize_Control
	{
    	/**
       	* Render the content on the theme customizer page
       	*/
       	public function render_content()
       	{
       		?>
       		<label>
       			<strong class="customize-text_editor"><?php echo esc_html( $this->label ); ?></strong>
       			<br />
       			<span class="customize-text_editor_desc">
       				<?php echo wp_kses_post( $this->description ); ?>
       			</span>
       		</label>
       		<?php
       	}
    }//editor close
    
}//class close

