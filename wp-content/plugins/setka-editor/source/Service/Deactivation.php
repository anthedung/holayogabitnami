<?php
namespace Setka\Editor\Service;

class Deactivation {

    public static function run() {
        register_uninstall_hook(
            PathsAndUrls::getPlugin()->get_path(),
            array('\Setka\Editor\Service\Uninstall', 'run')
        );
    }
}
