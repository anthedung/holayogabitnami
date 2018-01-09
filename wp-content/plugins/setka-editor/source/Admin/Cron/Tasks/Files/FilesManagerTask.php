<?php
namespace Setka\Editor\Admin\Cron\Tasks\Files;

use Setka\Editor\Admin\Prototypes\Cron;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Service\FilesManager\Exceptions\SyncDisabledByUseException;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;

class FilesManagerTask extends Cron\AbstractTask {

    public function __construct() {
        $this->setTimestamp(1);
        $this->setOnce(false);
        $this->setRecurrence(Plugin::_NAME_ . '_every_minute');
        $this->setHook(Plugin::_NAME_ . '_cron_files_manager');
    }

    public function execute() {
        if(!Account::is_logged_in())
            return;

        $filesManager = FilesManagerFactory::create();
        try {
            $filesManager->run();
        }
        catch (SyncDisabledByUseException $exception) {
            $filesManager->disableSyncingTasks();
            return;
        }
        catch (\Exception $exception) {
            // Deal with that
            // For example: this can happens if no JSON config found in DB
        }
    }
}
