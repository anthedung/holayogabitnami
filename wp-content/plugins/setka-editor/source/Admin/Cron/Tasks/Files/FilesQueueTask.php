<?php
namespace Setka\Editor\Admin\Cron\Tasks\Files;

use Setka\Editor\Admin\Prototypes\Cron;
use Setka\Editor\Admin\Options;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;

class FilesQueueTask extends Cron\AbstractTask {

    public function __construct() {
        $this->setTimestamp(1);
        $this->setOnce(false);
        $this->setRecurrence('hourly');
        $this->setHook(Plugin::_NAME_ . '_cron_files_queue');
    }

    public function execute() {
        if(!Account::is_logged_in())
            return;

        try {
            $manager = FilesManagerFactory::create();
            $manager->checkPendingFiles();
        } catch (\Exception $exception) {
            // Deal with that
        }
    }
}
