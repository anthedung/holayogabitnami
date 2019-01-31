<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

								//======================================================================
													// MT Other plugins Class 
								//======================================================================

class MT_OTHER_PLUGINS {

		/*
		* __construct initialize all function of this class.
		* Returns nothing. 
		* Used action_hooks to get things sequentially.
		*/ 
		function __construct(){
			 /*
	         * admin_menu hooks fires on wp admin load.
	         * Add the Sub menu page in wp admin area.
	         */
	        // add_action( 'admin_menu', array( $this, 'mt_sub_menu' ) );	

		}/* __construct Method ends here. */

	
		/*
		* mt_sub_menu holds the new page functionality.
		*/ 
		function mt_sub_menu(){
			$Easy_Facebook_Likebox_Admin = new Easy_Facebook_Likebox_Admin();

			/*
	         * add_submenu_page will add sub menu into the floating links.
	         */
			  add_submenu_page( 
			  	$Easy_Facebook_Likebox_Admin->plugin_slug, 
			  	__('My Plugins', 'easy-facebook-likebox'),  
			  	__('My Plugins', 'easy-facebook-likebox'), 
			  	'manage_options', 
			  	'mt-other-plugins', 
			  	array($this,'mt_other_plugins_content') 
			  );
			
		}/* mt_sub_menu Method ends here. */	

		/*
		 * mif_page_cb contains the html/markup of the page.
	     * Returns nothing.
	     */
	    public function mt_other_plugins_content(){

	    	/*
	         * Our Plugins tab html.
	         * mif_other_plugins_html filter can be used to customize our plugins tab html.
	         */
	        $mt_op_html = null;
	        $mt_op_html .= '<h2 class="nav-tab-wrapper">

                <a href="'.admin_url("admin.php").'?page=easy-facebook-likebox&tab=general" class="nav-tab">'.__("General", 'easy-facebook-likebox').'</a>
                <a href="'.admin_url("admin.php").'?page=easy-facebook-likebox&tab=autopopup" class="nav-tab ">'.__("Auto PopUp", 'easy-facebook-likebox').'</a>
             	<a href="'.admin_url("admin.php").'?page=easy-facebook-likebox&tab=clear_cache" class="nav-tab ">'.__("Clear Cache", 'easy-facebook-likebox').'</a>
             	<a href="'.admin_url("admin.php").'?page=easy-facebook-likebox&tab=myplugins" class="nav-tab nav-tab-active">'.__("My Plugins", 'easy-facebook-likebox').'</a>
		        <a href="'.admin_url("admin.php").'?page=easy-facebook-likebox&tab=supportupdates" class="nav-tab ">'.__("Support and Updates", 'easy-facebook-likebox').'</a>             
                 
            </h2>
	        			<div id="mt-other-plugins" class="">
						 <!-- Our Plugins  HTML-->
						 <div class="mt_our_plugins_iframe"><iframe src="https://maltathemes.com/our-plugins/" height="400" width="680"  style="border:0px;float:left;" id="mt-our-plugins" name="Our Plugins"></iframe></div><style>.mt_our_plugins_iframe{position:relative;padding-bottom:55%;height:0;overflow: hidden;max-width:100%;}.mt_our_plugins_iframe iframe{position:absolute; top: 12px;left:0;width:100%;height:100%;}</style>
						<!-- Our Plugins  HTML Ends-->	
						</div>';

	        $mt_op_html = apply_filters( 'mt_op_html', $mt_op_html );

	        echo $mt_op_html;


	    }	

}/* MT_OTHER_PLUGINS class ends here. */	
$MT_OTHER_PLUGINS = new MT_OTHER_PLUGINS();