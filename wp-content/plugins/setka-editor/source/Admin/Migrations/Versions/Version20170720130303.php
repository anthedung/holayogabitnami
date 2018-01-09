<?php
namespace Setka\Editor\Admin\Migrations\Versions;

use Setka\Editor\Admin\Migrations\MigrationInterface;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Service\Config\PluginConfig;
use Setka\Editor\Service\SetkaAccount\Account;

class Version20170720130303 implements MigrationInterface {

    public function up() {

        // This migration only for logged in users.

        if(!Account::is_logged_in()) {
            return;
        }

        if(PluginConfig::isVIP()) {
        	// VIP env not using this type of files sync.
        	return;
        }

        // Start syncing files.
        $manager = FilesManagerFactory::create();
        $manager
            ->restartSyncing()

            // Just clean tasks to prevent double registering tasks
            ->disableSyncingTasks()

            // Register tasks
            ->enableSyncingTasks();
    }
}
