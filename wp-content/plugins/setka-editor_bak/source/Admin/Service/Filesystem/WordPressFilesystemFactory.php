<?php
namespace Setka\Editor\Admin\Service\Filesystem;

class WordPressFilesystemFactory
{

    /**
     * Returns WordPress filesystem object.
     *
     * @throws \Exception If WordPress can't create Filesystem instance.
     *
     * @return \WP_Filesystem_Base
     */
    public static function create()
    {
        require_once(ABSPATH . '/wp-admin/includes/file.php');

        if(WP_Filesystem()) {
            global $wp_filesystem;
            return $wp_filesystem;
        }

        throw new \Exception();
    }
}
