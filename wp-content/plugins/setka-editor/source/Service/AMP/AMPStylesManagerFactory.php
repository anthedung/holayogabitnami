<?php
namespace Setka\Editor\Service\AMP;

use Psr\Log\LoggerInterface;
use Setka\Editor\Admin\Options\AMP\AMPStylesIdOption;
use Setka\Editor\Admin\Options\AMP\AMPStylesOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncAttemptsLimitFailureOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncFailureNoticeOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncFailureOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncLastFailureNameOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncStageOption;
use Setka\Editor\Admin\Options\AMP\UseAMPStylesOption;
use Setka\Editor\Admin\Service\ContinueExecution\CronLock;
use Setka\Editor\Admin\Service\FilesSync\WordPressDownloader;
use Setka\Editor\Admin\Service\Filesystem\FilesystemFactory;
use Setka\Editor\PostMetas\AttemptsToDownloadPostMeta;
use Setka\Editor\PostMetas\OriginUrlPostMeta;
use Setka\Editor\PostMetas\SetkaFileTypePostMeta;
use Setka\Editor\Service\DataFactory;

class AMPStylesManagerFactory
{
    /**
     * Create new instance of AMPStylesManager.
     *
     * @param AMPStylesIdOption $ampStylesIdOption
     * @param AMPStylesOption $ampStylesOption
     * @param AMPSyncAttemptsLimitFailureOption $ampSyncAttemptsLimitFailureOption
     * @param AMPSyncFailureNoticeOption $ampSyncFailureNoticeOption
     * @param AMPSyncFailureOption $ampSyncFailureOption
     * @param AMPSyncLastFailureNameOption $ampSyncLastFailureNameOption
     * @param AMPSyncOption $ampSyncOption
     * @param AMPSyncStageOption $ampSyncStageOption
     * @param UseAMPStylesOption $useAMPStylesOption
     *
     * @param DataFactory $dataFactory
     *
     * @param $downloadAttempts int
     * @param $maxFileSize int
     *
     * @return AMPStylesManager
     */
    public static function create(
        LoggerInterface $logger,
        AMPStylesIdOption $ampStylesIdOption,
        AMPStylesOption $ampStylesOption,
        AMPSyncAttemptsLimitFailureOption $ampSyncAttemptsLimitFailureOption,
        AMPSyncFailureNoticeOption $ampSyncFailureNoticeOption,
        AMPSyncFailureOption $ampSyncFailureOption,
        AMPSyncLastFailureNameOption $ampSyncLastFailureNameOption,
        AMPSyncOption $ampSyncOption,
        AMPSyncStageOption $ampSyncStageOption,
        UseAMPStylesOption $useAMPStylesOption,
        DataFactory $dataFactory,
        $downloadAttempts,
        $maxFileSize
    ) {
        if(defined('DOING_CRON') && true === DOING_CRON) {
            $continueExecution = array(CronLock::class, 'check');
        } else {
            $continueExecution = '__return_true';
        }

        /**
         * @var $originUrlPostMeta OriginUrlPostMeta
         * @var $setkaFileTypePostMeta SetkaFileTypePostMeta
         * @var $attemptsToDownloadPostMeta AttemptsToDownloadPostMeta
         */
        $downloader                 = new WordPressDownloader();
        $originUrlPostMeta          = $dataFactory->create(OriginUrlPostMeta::class);
        $setkaFileTypePostMeta      = $dataFactory->create(SetkaFileTypePostMeta::class);
        $attemptsToDownloadPostMeta = $dataFactory->create(AttemptsToDownloadPostMeta::class);

        $fileSystem = FilesystemFactory::create();

        return new AMPStylesManager(
            $continueExecution,
            $logger,
            $ampStylesIdOption,
            $ampStylesOption,
            $ampSyncAttemptsLimitFailureOption,
            $ampSyncFailureNoticeOption,
            $ampSyncFailureOption,
            $ampSyncLastFailureNameOption,
            $ampSyncOption,
            $ampSyncStageOption,
            $useAMPStylesOption,
            $downloader,
            $fileSystem,
            $originUrlPostMeta,
            $setkaFileTypePostMeta,
            $attemptsToDownloadPostMeta,
            $downloadAttempts,
            $maxFileSize
        );
    }
}
