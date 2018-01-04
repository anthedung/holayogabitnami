jQuery(document).ready(function() {

	/* Tabs in welcome page */
	function kerinci_welcome_page_tabs(event) {
		jQuery(event).parent().addClass("active");
        jQuery(event).parent().siblings().removeClass("active");
        var tab = jQuery(event).attr("href");
        jQuery(".kerinci-tab-pane").not(tab).css("display", "none");
        jQuery(tab).fadeIn();
	}

	var kerinci_actions_anchor = location.hash;

	if( (typeof kerinci_actions_anchor !== 'undefined') && (kerinci_actions_anchor != '') ) {
		kerinci_welcome_page_tabs('a[href="'+ kerinci_actions_anchor +'"]');
	}

    jQuery(".kerinci-nav-tabs a").click(function(event) {
        event.preventDefault();
		kerinci_welcome_page_tabs(this);
    });

		/* Tab Content height matches admin menu height for scrolling purpouses */
	 $tab = jQuery('.kerinci-tab-content > div');
	 $admin_menu_height = jQuery('#adminmenu').height();
	 if( (typeof $tab !== 'undefined') && (typeof $admin_menu_height !== 'undefined') )
	 {
		 $newheight = $admin_menu_height - 200;
		 $tab.css('min-height',$newheight);
	 }

});
