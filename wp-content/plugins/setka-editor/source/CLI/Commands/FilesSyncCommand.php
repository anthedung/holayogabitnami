<?php
namespace Setka\Editor\CLI\Commands;

use Setka\Editor\Admin\Options\Files\FileSyncStageOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Service\FilesManager\FilesManager;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use WP_CLI as Console;

class FilesSyncCommand extends \WP_CLI_Command
{

    /**
     * @var FilesManager
     */
    protected $manager;

    public function __construct()
    {
        parent::__construct();

        $this->manager = FilesManagerFactory::create();
    }


    /**
     * Restart syncing.
     *
     * @when after_wp_load
     */
    public function restart()
    {

        $this->manager->restartSyncing();

        $useLocalFilesOption = new UseLocalFilesOption();
        if('1' === $useLocalFilesOption->getValue()) {
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
            sprintf('File sync stage: %s', $fileSyncStageOption->getValue())
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

        $this->manager
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
        $this->manager
            ->disableLocalUsage()
            ->disableSyncingTasks();

        Console::success('Syncing was disabled');
    }
}
