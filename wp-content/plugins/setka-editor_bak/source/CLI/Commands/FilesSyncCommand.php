<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Admin\Options\Files\FileSyncStageOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Service\FilesManager\FilesManager;
use WP_CLI as Console;

/**
 * Class FilesSyncCommand
 */
class FilesSyncCommand extends \WP_CLI_Command
{
    /**
     * @var FilesManager
     */
    protected $filesManager;

    /**
     * FilesSyncCommand constructor.
     * @param FilesManager $filesManager
     */
    public function __construct(FilesManager $filesManager)
    {
        parent::__construct();
        $this->filesManager = $filesManager;
    }


    /**
     * Restart syncing.
     *
     * @when after_wp_load
     */
    public function restart()
    {

        $this->filesManager->restartSyncing();

        $useLocalFilesOption = new UseLocalFilesOption();
        if(true === $useLocalFilesOption->get()) {
            $state = 'yes';
        } else {
            $state = 'no';
        }
        Console::log(
            sprintf('Using local files: %s', $state)
        );
        unset($useLocalFilesOption, $state);

        $fileSyncStageOption = new FileSyncStageOption();
        Console::log(
            sprintf('File sync stage: %s', $fileSyncStageOption->get())
        );

        Console::success('Sync restarted.');
    }

    /**
     * Enable files sync. Setups required Cron tasks.
     *
     * @when after_wp_load
     *
     * @alias on
     */
    public function enable()
    {

        $this->restart();

        $this->filesManager
            ->disableSyncingTasks()
            ->enableSyncingTasks();

        Console::success('Syncing was enabled.');
    }

    /**
     * Disable files sync. Removes Cron tasks.
     *
     * @when after_wp_load
     *
     * @alias off
     */
    public function disable()
    {
        $this->filesManager
            ->disableLocalUsage()
            ->disableSyncingTasks();

        Console::success('Syncing was disabled');
    }
}
