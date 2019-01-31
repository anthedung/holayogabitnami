<?php
namespace Setka\Editor\Admin\Service\FilesManager;

use Setka\Editor\Admin\Options\Files\FileSyncFailureOption;
use Setka\Editor\Admin\Options\Files\FileSyncOption;
use Setka\Editor\Admin\Options\Files\FileSyncStageOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Service\ContinueExecution\CronLock;
use Setka\Editor\Admin\Service\FilesSync\Synchronizer;
use Setka\Editor\Service\DataFactory;

/**
 * Class FilesManagerFactory
 */
class FilesManagerFactory
{
    /**
     * Creates FileWatcher instance and setup continueExecution callback.
     *
     * @param $dataFactory DataFactory
     * @param $downloadListOfFiles DownloadListOfFiles
     * @param $synchronizer Synchronizer
     * @param $downloadAttempts int
     *
     * @return FilesManager
     */
    public static function create(
        DataFactory $dataFactory,
        DownloadListOfFiles $downloadListOfFiles,
        Synchronizer $synchronizer,
        $downloadAttempts = 3
    ) {
        if(defined('DOING_CRON') && true === DOING_CRON) {
            $continueExecution = array(CronLock::class, 'check');
        } else {
            $continueExecution = '__return_true';
        }

        /**
         * @var $fileSyncFailureOption FileSyncFailureOption
         * @var $fileSyncOption FileSyncOption
         * @var $fileSyncStageOption FileSyncStageOption
         * @var $useLocalFilesOption UseLocalFilesOption
         */
        $fileSyncFailureOption = $dataFactory->create(FileSyncFailureOption::class);
        $fileSyncOption        = $dataFactory->create(FileSyncOption::class);
        $fileSyncStageOption   = $dataFactory->create(FileSyncStageOption::class);
        $useLocalFilesOption   = $dataFactory->create(UseLocalFilesOption::class);

        return new FilesManager(
            $continueExecution,
            $fileSyncFailureOption,
            $fileSyncOption,
            $fileSyncStageOption,
            $useLocalFilesOption,
            $downloadListOfFiles,
            $synchronizer,
            $downloadAttempts
        );
    }
}
