<?php
namespace Setka\Editor\Admin\Service;

use Setka\Editor\Admin\Migrations\ConfigurationFactory;

class Migrations {

    public static function run() {
        try {
            $configuration = ConfigurationFactory::create();
            $configuration->migrateAsNecessary();
        } catch (\Exception $exception) {
            // Just catch
        }
    }
}
