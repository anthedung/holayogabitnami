<?php
namespace Setka\Editor\Admin\Service;

use Setka\Editor\Service\PathsAndUrls;

class Freemius {

	public static function run() {
        global $setka_editor_freemius;

        if(!isset($setka_editor_freemius)) {
            // Include Freemius SDK.
            require_once PathsAndUrls::get_plugin_dir_path('source/libraries/freemius/wordpress-sdk/start.php');

            $setka_editor_freemius = fs_dynamic_init( array(
                'id'                  => '1245',
                'slug'                => 'setka-editor',
                'type'                => 'plugin',
                'public_key'          => 'pk_15103d9fe899fc27028a14a3d656f',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'setka-editor',
                    'account'        => false,
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        // Signal that SDK was initiated.
        do_action( 'setka_editor_freemius_loaded' );
	}
}
