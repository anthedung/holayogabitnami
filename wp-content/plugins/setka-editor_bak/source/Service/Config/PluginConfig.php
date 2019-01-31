<?php
namespace Setka\Editor\Service\Config;

class PluginConfig
{

    public static function getUpgradeUrl()
    {
        return apply_filters('setka_editor_upgrade_url', 'https://editor.setka.io/app/billing_plans');
    }

    /**
     * Check for wp.com env.
     *
     * @return bool True if WordPress.com env, false otherwise.
     */
    public static function isVIP()
    {
        if (defined('WPCOM_IS_VIP_ENV') && true === WPCOM_IS_VIP_ENV) {
            // Running on WordPress.com
            return true;
        }
        return false;
    }

    /**
     * Check for Gutenberg env.
     *
     * @return bool True if Gutenberg activated.
     */
    public static function isGutenberg()
    {
        $plugins = get_option('active_plugins');

        if(is_array($plugins) && in_array('classic-editor/classic-editor.php', $plugins, true)) {
            return false;
        }

        if(function_exists('register_block_type')
            &&
            function_exists('do_blocks')
            &&
            function_exists('parse_blocks')
            &&
            function_exists('wp_set_script_translations')
        ) {
            return true;
        }
        return false;
    }
}
