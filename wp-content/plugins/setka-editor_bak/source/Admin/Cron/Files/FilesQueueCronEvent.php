<?php
namespace Setka\Editor\Admin\Cron\Files;

use Korobochkin\WPKit\Cron\AbstractCronEvent;
use Setka\Editor\Admin\Service\FilesManager\FilesManager;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class FilesQueueCronEvent
 */
class FilesQueueCronEvent extends AbstractCronEvent
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * @var FilesManager
     */
    protected $filesManager;

    /**
     * FilesQueueCronEvent constructor.
     */
    public function __construct()
    {
        $this
            ->setTimestamp(1)
            ->setRecurrence('hourly')
            ->setName(Plugin::_NAME_ . '_cron_files_queue');
    }

    public function execute()
    {
        if(!$this->setkaEditorAccount->isLoggedIn()) {
            return $this;
        }

        try {
            $this->filesManager->checkPendingFiles();
        } catch (\Exception $exception) {
            // Deal with that
        }
        return $this;
    }

    /**
     * @return SetkaEditorAccount
     */
    public function getSetkaEditorAccount()
    {
        return $this->setkaEditorAccount;
    }

    /**
     * @param SetkaEditorAccount $setkaEditorAccount
     * @return $this
     */
    public function setSetkaEditorAccount(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
        return $this;
    }

    /**
     * @return FilesManager
     */
    public function getFilesManager()
    {
        return $this->filesManager;
    }

    /**
     * @param FilesManager $filesManager
     * @return $this
     */
    public function setFilesManager(FilesManager $filesManager)
    {
        $this->filesManager = $filesManager;
        return $this;
    }
}
