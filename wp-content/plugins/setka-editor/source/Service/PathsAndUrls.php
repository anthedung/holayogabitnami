<?php
namespace Setka\Editor\Service;

use Setka\Editor\Plugin;

class PathsAndUrls
{

    /**
     * @var Plugin
     */
    protected static $plugin;

    /**
     * @return Plugin
     */
    public static function getPlugin()
    {
        return self::$plugin;
    }

    /**
     * @param Plugin $plugin
     */
    public static function setPlugin($plugin)
    {
        self::$plugin = $plugin;
    }

    public static function getPluginDirPath($sub_path = null)
    {
        $path = self::getPlugin()->getDir();

        if(is_string($sub_path) && !empty($sub_path)) {
            $path .= $sub_path;
        }

        return $path;
    }

    public static function getVendorPath($sub_path = null)
    {
        $vendor_path = self::getPluginDirPath('vendor/');

        if(is_string($sub_path) && !empty($sub_path)) {
            $vendor_path .= $sub_path;
        }

        return $vendor_path;
    }

    public static function getPluginUrl($sub_path = null)
    {
        $url = plugin_dir_url(self::getPlugin()->getFile());

        if(is_string($sub_path) && !empty($sub_path)) {
            $url .= $sub_path;
        }

        return $url;
    }

    public static function getPluginBasenamePath($path_to_add = null)
    {
        $path = basename(dirname(self::getPlugin()->getFile()));

        if(is_string($path_to_add) && !empty($path_to_add)) {
            $path  = trailingslashit($path);
            $path .= $path_to_add;
        }

        return $path;
    }

    /**
     * Simply util that split path into array of sub paths in hirerarhical order.
     *
     * @param $path string Path to file or page
     *
     * @return array
     */
    public static function splitUrlPathIntoFragments($path)
    {
        $fragments   = array();
        $fragment    = $path;
        $fragments[] = $fragment;

        while('/' !== $fragment) {
            $fragment    = dirname($fragment);
            $fragments[] = $fragment;
        }

        return $fragments;
    }
}
