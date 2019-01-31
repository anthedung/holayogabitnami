<?php
namespace Setka\Editor\Admin\Cron\Files;

use Korobochkin\WPKit\Cron\AbstractCronEvent;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;

class FilesQueueCronEvent extends AbstractCronEvent
{

    public function __construct()
    {
        $this->setTimestamp(1);
        $this->setRecurrence('hourly');
        $this->setName(Plugin::_NAME_ . '_cron_files_queue');
    }

    public function execute()
    {
        if(!Account::isLoggedIn()) {
            return;
        }

        try {
            $manager = FilesManagerFactory::create();
            $manager->checkPendingFiles();
        } catch (\Exception $exception) {
            // Deal with that
        }
    }
}
