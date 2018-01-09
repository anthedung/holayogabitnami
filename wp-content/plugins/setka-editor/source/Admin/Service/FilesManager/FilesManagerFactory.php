<?php
namespace Setka\Editor\Admin\Service\FilesManager;

use Setka\Editor\Admin\Options\Files\FileSyncFailureOption;
use Setka\Editor\Admin\Options\Files\FileSyncOption;
use Setka\Editor\Admin\Options\Files\FileSyncStageOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Service\ContinueExecution\CronLock;

class FilesManagerFactory {

	/**
	 * Creates FileWatcher instance and setup continueExecution callback.
	 *
	 * @return FilesManager
	 */
	public static function create() {

        if(defined('DOING_CRON') && true === DOING_CRON) {
            $continueExecution = array(CronLock::class, 'check');
        } else {
            $continueExecution = '__return_true';
        }

        $fileSyncFailureOption = new FileSyncFailureOption();
        $fileSyncOption = new FileSyncOption();
        $fileSyncStageOption = new FileSyncStageOption();
        $useLocalFilesOption = new UseLocalFilesOption();

        return new FilesManager(
            $continueExecution,
            $fileSyncFailureOption,
            $fileSyncOption,
            $fileSyncStageOption,
            $useLocalFilesOption
        );
	}
}
