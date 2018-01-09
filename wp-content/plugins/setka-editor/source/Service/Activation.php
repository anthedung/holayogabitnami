<?php
namespace Setka\Editor\Service;

use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\User\Capabilities;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;
use Setka\Editor\Service\SetkaAccount\SignIn;
use Setka\Editor\Admin\Transients;

class Activation {

	/**
	 * Running on plugin activation.
	 *
	 * @since 0.0.1
	 */
	public static function run() {
		/**
		 * Setup settings only if they don't exist. This check prevent data loosing.
		 */
		if( self::is_activated_first_time() ) {
			self::setup_capabilities();

            $dbVersion = new Options\DBVersion\DBVersionOption();
            $dbVersion->updateValue(Plugin::DB_VERSION);
		}

        if( !Account::is_logged_in() ) {
            SignIn::signInAnonymous();
            $afterSignInTransient = new Transients\AfterSignInNotice\Transient();
            $afterSignInTransient->updateValue('1');
        }
	}

	/**
	 * Setup required capabilities for User Roles. This settings can be changed on plugin
	 * settings page or with Members plugin (https://wordpress.org/plugins/members/).
	 *
	 * @since 0.0.1
	 */
	public static function setup_capabilities() {
		$roles = get_editable_roles();

		if( !empty( $roles ) ) {
			foreach( $roles as $role_key => $role_value ) {
				if( in_array( $role_key, array( 'administrator', 'editor', 'author', 'contributor' ), true ) ) {

					$role = get_role( $role_key );
					$role->add_cap( Capabilities\UseEditorCapability::NAME );

				}
			}
		}
	}

	/**
	 * Checkout if plugin activated first time.
	 *
	 * @since 0.0.1
	 *
	 * @return bool true if activated first time, false if activated not first time (any plugin setting found in DB).
	 */
	public static function is_activated_first_time() {
		$roles = get_editable_roles();

		// Search capabilities
		if( !empty( $roles ) ) {
			foreach( $roles as $role_key => $role_value ) {
				if( isset( $role_value['capabilities'][Capabilities\UseEditorCapability::NAME] ) ) {
					return false;
				}
			}
		}

		unset($roles, $role_key, $role_value);

		// If any options (settings) founded in DB then plugin activated previously
		if( Options\Common\Utilities::is_options_exists_in_db() ) {
			return false;
		}

		return true;
	}
}
