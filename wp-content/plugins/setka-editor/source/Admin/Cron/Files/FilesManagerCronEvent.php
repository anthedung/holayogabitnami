<?php
namespace Setka\Editor\Admin\Cron\Files;

use Korobochkin\WPKit\Cron\AbstractCronEvent;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Service\FilesManager\Exceptions\SyncDisabledByUseException;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;

class FilesManagerCronEvent extends AbstractCronEvent
{

    public function __construct()
    {
        $this->setTimestamp(1);
        $this->setRecurrence(Plugin::_NAME_ . '_every_minute');
        $this->setName(Plugin::_NAME_ . '_cron_files_manager');
    }

    public function execute()
    {
        if(!Account::isLoggedIn()) {
            return;
        }

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
