<?php
namespace Setka\Editor\Service;

/**
 * Class PathsAndUrls
 */
class PathsAndUrls
{
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
