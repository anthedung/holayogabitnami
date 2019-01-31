<?php
namespace Setka\Editor\Service;

class Deactivation
{

    public static function run()
    {
        register_uninstall_hook(
            PathsAndUrls::getPlugin()->getFile(),
            array('\Setka\Editor\Service\Uninstall', 'run')
        );
    }
}
