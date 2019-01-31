<?php
namespace Setka\Editor\Admin\Cron\Files;

use Korobochkin\WPKit\Cron\AbstractCronEvent;
use Setka\Editor\Admin\Service\FilesManager\Exceptions\SyncDisabledByUseException;
use Setka\Editor\Admin\Service\FilesManager\FilesManager;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class FilesManagerCronEvent
 */
class FilesManagerCronEvent extends AbstractCronEvent
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
     * FilesManagerCronEvent constructor.
     */
    public function __construct()
    {
        $this
            ->setTimestamp(1)
            ->setRecurrence(Plugin::_NAME_ . '_every_minute')
            ->setName(Plugin::_NAME_ . '_cron_files_manager');
    }

    public function execute()
    {
        if(!$this->setkaEditorAccount->isLoggedIn()) {
            return $this;
        }

        try {
            $this->filesManager->run();
        }
        catch (SyncDisabledByUseException $exception) {
            $this->filesManager->disableSyncingTasks();
        }
        catch (\Exception $exception) {
            // Deal with that
            // For example: this can happens if no JSON config found in DB
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
