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
}
