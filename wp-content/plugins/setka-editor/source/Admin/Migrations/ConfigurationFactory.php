<?php
namespace Setka\Editor\Admin\Migrations;

use Setka\Editor\Admin\Migrations\Versions;
use Setka\Editor\Admin\Options\DBVersion\DBVersionOption;
use Setka\Editor\Plugin;

class ConfigurationFactory {

    public static function create() {
        $dbVersionOption = new DBVersionOption();
        $pluginVersion = Plugin::DB_VERSION;

        $versions = array(
	        Versions\Version20170720130303::class,
        );

        return new Configuration($dbVersionOption, $pluginVersion, $versions);
    }
}
