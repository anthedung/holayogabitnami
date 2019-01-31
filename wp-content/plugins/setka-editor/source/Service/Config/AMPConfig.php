<?php
namespace Setka\Editor\Service\Config;

class AMPConfig
{
    /**
     * Check for AMP plugin.
     *
     * @return bool True if AMP plugin enabled, false if disabled.
     */
    public static function isPluginActivate()
    {
        return function_exists('amp_init');
    }

    /**
     * Return AMP plugin mode.
     *
     * @return string|bool 'native', 'paired' or 'classic' mode. False if AMP plugin not enabled.
     */
    public static function getMode()
    {
        if (!self::isPluginActivate()) {
            return false;
        }

        if (!function_exists('amp_is_canonical')) {
            return false;
        }

        if (amp_is_canonical()) {
            $mode = 'native';
        } elseif (current_theme_supports('amp')) {
            $mode = 'paired';
        } else {
            $options = get_option('amp-options');
            if (isset($options['theme_support']) && is_string($options['theme_support'])) {
                if('disabled' === $options['theme_support']) {
                    $mode = 'classic';
                } else {
                    $mode =& $options['theme_support'];
                }
            } else {
                $mode = 'classic';
            }
        }

        return $mode;
    }

    /**
     * Check if AMP page requested.
     *
     * @return bool True if AMP page requested.
     */
    public static function isAMPEndpoint()
    {
        if(function_exists('is_amp_endpoint')) {
            return is_amp_endpoint();
        }
        return false;
    }
}
