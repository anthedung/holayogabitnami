<?php
namespace Setka\Editor\Admin\Migrations\Versions;

use Setka\Editor\Admin\Migrations\MigrationInterface;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Service\Config\PluginConfig;
use Setka\Editor\Service\SetkaAccount\Account;

class Version20170720130303 implements MigrationInterface
{

    public function up()
    {
        if(!Account::isLoggedIn()) {
            return $this;
        }

        if(PluginConfig::isVIP()) {
            return $this;
        }

        $manager = FilesManagerFactory::create();
        $manager
            ->restartSyncing()
            ->disableSyncingTasks()
            ->enableSyncingTasks();

        return $this;
    }
}
