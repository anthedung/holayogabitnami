<?php 
/**
* Promotional Section
*
* @package pranayama_yoga
*/   
    $content     = get_theme_mod('pranayama_yoga_promotional_section_title');
    $description = get_theme_mod('pranayama_yoga_promotional_section_description');
    $button_text = get_theme_mod('pranayama_yoga_call_to_action_button', __('Book Now', 'pranayama-yoga'));
    $button_link = get_theme_mod('pranayama_yoga_call_to_action_button_link');
?>  
    <div class="section-four">
	    <div class="container">
		    <div class="text">
				
				<?php 
				if( $content ) 
				    echo '<h2>' . esc_html( $content ) . '</h2>';

				if( $description ) 
				   echo wpautop( wp_kses_post( $description ) );

				if( $button_text || $button_link ): 
				   echo '<a href= " ' . esc_url( $button_link ) . '" class="btn">';
				       echo esc_html( $button_text );
				   echo '</a>';
	            endif; ?>

			</div>
		</div>
	</div>