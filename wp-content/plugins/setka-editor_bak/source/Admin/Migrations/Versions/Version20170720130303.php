<?php
namespace Setka\Editor\Admin\Migrations\Versions;

use Setka\Editor\Admin\Migrations\MigrationInterface;
use Setka\Editor\Admin\Service\FilesManager\FilesManager;
use Setka\Editor\Service\Config\PluginConfig;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class Version20170720130303
 */
class Version20170720130303 implements MigrationInterface
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
     * Version20170720130303 constructor.
     * @param SetkaEditorAccount $setkaEditorAccount
     * @param FilesManager $filesManager
     */
    public function __construct(SetkaEditorAccount $setkaEditorAccount, FilesManager $filesManager)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
        $this->filesManager       = $filesManager;
    }

    /**
     * @inheritdoc
     */
    public function up()
    {
        if(!$this->setkaEditorAccount->isLoggedIn()) {
            return $this;
        }

        if(PluginConfig::isVIP()) {
            return $this;
        }

        $this->filesManager
            ->restartSyncing()
            ->disableSyncingTasks()
            ->enableSyncingTasks();

        return $this;
    }
}
