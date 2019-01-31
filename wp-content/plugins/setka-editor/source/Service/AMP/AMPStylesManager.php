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
use Setka\Editor\Admin\Service\ContinueExecution\OutOfTimeException;
use Setka\Editor\Admin\Service\FilesSync\DownloaderInterface;
use Setka\Editor\Admin\Service\FilesSync\Exceptions\FileDownloadException;
use Setka\Editor\Admin\Service\Filesystem\FilesystemInterface;
use Setka\Editor\Admin\Service\WPQueryFactory;
use Setka\Editor\PostMetas\AttemptsToDownloadPostMeta;
use Setka\Editor\PostMetas\OriginUrlPostMeta;
use Setka\Editor\PostMetas\SetkaFileTypePostMeta;
use Setka\Editor\Service\AMP\Exceptions\JsonDecodeException;
use Setka\Editor\Service\AMP\Exceptions\JsonEncodeException;
use Setka\Editor\Service\AMP\Exceptions\MaxFileSizeException;
use Setka\Editor\Service\AMP\Exceptions\NoAMPConfigException;
use Setka\Editor\Service\AMP\Exceptions\PendingFilesException;
use Setka\Editor\Service\AMP\Exceptions\PostException;
use Setka\Editor\Service\AMP\Exceptions\PostMetaException;
use Setka\Editor\Service\AMP\Exceptions\ReadFileException;
use Setka\Editor\Service\PostStatuses;
use Setka\Editor\Service\SetkaPostTypes;

/**
 * Class AMPStylesManager downloads and saves CSS files for AMP pages.
 */
class AMPStylesManager
{
    /**
     * @var callable Callback which checked after each iteration in $this->syncFiles().
     */
    protected $continueExecution;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AMPStylesIdOption
     */
    protected $ampStylesIdOption;

    /**
     * @var AMPStylesOption
     */
    protected $ampStylesOption;

    /**
     * @var \WP_Post
     */
    protected $lastAMPStylesPost;

    /**
     * @var AMPSyncAttemptsLimitFailureOption
     */
    protected $ampSyncAttemptsLimitFailureOption;

    /**
     * @var AMPSyncFailureNoticeOption
     */
    protected $ampSyncFailureNoticeOption;

    /**
     * @var AMPSyncFailureOption
     */
    protected $ampSyncFailureOption;

    /**
     * @var AMPSyncLastFailureNameOption
     */
    protected $ampSyncLastFailureNameOption;

    /**
     * @var AMPSyncOption
     */
    protected $ampSyncOption;

    /**
     * @var AMPSyncStageOption
     */
    protected $ampSyncStageOption;

    /**
     * @var UseAMPStylesOption
     */
    protected $useAMPStylesOption;

    /**
     * @var DownloaderInterface
     */
    protected $downloader;

    /**
     * @var FilesystemInterface
     */
    protected $fileSystem;

    /**
     * @var OriginUrlPostMeta
     */
    protected $originUrlPostMeta;

    /**
     * @var SetkaFileTypePostMeta
     */
    protected $setkaFileTypePostMeta;

    /**
     * @var AttemptsToDownloadPostMeta
     */
    protected $attemptsToDownloadPostMeta;

    /**
     * @var int Number of download attempts.
     */
    protected $downloadAttempts = 3;

    /**
     * @var int Max file size in bytes.
     */
    protected $maxFileSize = 50000;

    /**
     * AMPStylesManager constructor.
     * @param callable $continueExecution
     * @param LoggerInterface $logger
     * @param AMPStylesIdOption $ampStylesIdOption
     * @param AMPStylesOption $ampStylesOption
     * @param AMPSyncAttemptsLimitFailureOption $ampSyncAttemptsLimitFailureOption
     * @param AMPSyncFailureNoticeOption $ampSyncFailureNoticeOption
     * @param AMPSyncFailureOption $ampSyncFailureOption
     * @param AMPSyncLastFailureNameOption $ampSyncLastFailureNameOption
     * @param AMPSyncOption $ampSyncOption
     * @param AMPSyncStageOption $ampSyncStageOption
     * @param UseAMPStylesOption $useAMPStylesOption
     * @param DownloaderInterface $downloader
     * @param FilesystemInterface $fileSystem
     * @param OriginUrlPostMeta $originUrlPostMeta
     * @param SetkaFileTypePostMeta $setkaFileTypePostMeta
     * @param AttemptsToDownloadPostMeta $attemptsToDownloadPostMeta
     * @param $downloadAttempts int
     * @param $maxFileSize int
     */
    public function __construct(
        callable $continueExecution,
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
        DownloaderInterface $downloader,
        FilesystemInterface $fileSystem,
        OriginUrlPostMeta$originUrlPostMeta,
        SetkaFileTypePostMeta $setkaFileTypePostMeta,
        AttemptsToDownloadPostMeta $attemptsToDownloadPostMeta,
        $downloadAttempts,
        $maxFileSize
    ) {
        $this->continueExecution = $continueExecution;

        $this->logger = $logger;

        $this->ampStylesIdOption                 = $ampStylesIdOption;
        $this->ampStylesOption                   = $ampStylesOption;
        $this->ampSyncAttemptsLimitFailureOption = $ampSyncAttemptsLimitFailureOption;
        $this->ampSyncFailureNoticeOption        = $ampSyncFailureNoticeOption;
        $this->ampSyncFailureOption              = $ampSyncFailureOption;
        $this->ampSyncLastFailureNameOption      = $ampSyncLastFailureNameOption;
        $this->ampSyncOption                     = $ampSyncOption;
        $this->ampSyncStageOption                = $ampSyncStageOption;
        $this->useAMPStylesOption                = $useAMPStylesOption;

        $this->downloader = $downloader;
        $this->fileSystem = $fileSystem;

        $this->originUrlPostMeta          = $originUrlPostMeta;
        $this->setkaFileTypePostMeta      = $setkaFileTypePostMeta;
        $this->attemptsToDownloadPostMeta = $attemptsToDownloadPostMeta;

        $this->downloadAttempts = $downloadAttempts;
        $this->maxFileSize      = $maxFileSize;
    }

    /**
     * Run manager.
     *
     * @throws OutOfTimeException If current cron process obsolete and we need to break execution.
     *
     * @throws NoAMPConfigException If AMP config was not found.
     * @throws JsonDecodeException If new config decoding was failed.
     *
     * @throws PostException If post was not created.
     * @throws PostMetaException If post meta was not updated.
     *
     * @throws MaxFileSizeException If one of requested file has large than allowed size.
     * @throws ReadFileException If downloaded file not readable.
     *
     * @throws PendingFilesException If all files was downloaded but there is pending files.
     *
     * @return $this For chain calls.
     */
    public function run()
    {
        $this->logger->info('Start running AMP styles manager.');

        if(!$this->ampSyncOption->get()) {
            $this->logger->info('Sync of AMP disabled by option. Stop executing.');
            return $this;
        }

        $this->lastAMPStylesPost = $lastAMPStylesPost = $this->findLastConfig();
        $currentConfigId         = (int) $this->ampStylesIdOption->get();

        if($lastAMPStylesPost->ID !== $currentConfigId) {
            $this->resetSync();
        }

        if($this->ampSyncAttemptsLimitFailureOption->get()) {
            $this->logger->info('Exceeded limit of download attempts of AMP files. Stop executing and exit.');
            return $this;
        }

        $stage = $this->ampSyncStageOption->get();

        $this->logger->debug('Got stage of AMP sync.', array('stage' => $stage));

        switch ($stage) {
            default:
            case AMPSyncStageOption::PREPARE_CONFIG:
                $this->logger->info('Start of stage.', array('stage' => $stage));

                $newConfig = $this->transformConfig($lastAMPStylesPost);
                $this->ampStylesOption->updateValue($newConfig);
                $this->ampStylesIdOption->updateValue($lastAMPStylesPost->ID);

                $stage = AMPSyncStageOption::RESET_PREVIOUS_STATE;
                $this->ampSyncStageOption->updateValue($stage);
                $this->logger->info('End of stage.', array('stage' => AMPSyncStageOption::PREPARE_CONFIG));
                // End of stage.

            case AMPSyncStageOption::RESET_PREVIOUS_STATE:
                $this->logger->info('Start of stage.', array('stage' => $stage));
                $this->continueExecution()->resetPreviousState()->removePreviousAMPConfigs();

                $stage = AMPSyncStageOption::CREATE_ENTRIES;
                $this->ampSyncStageOption->updateValue($stage);
                $this->logger->info('End of stage.', array('stage' => AMPSyncStageOption::RESET_PREVIOUS_STATE));
                // End of stage.

            case AMPSyncStageOption::CREATE_ENTRIES:
                $this->logger->info('Start of stage.', array('stage' => $stage));
                $this->continueExecution()->create();

                $stage = AMPSyncStageOption::REMOVE_OLD_ENTRIES;
                $this->ampSyncStageOption->updateValue($stage);
                $this->logger->info('End of stage.', array('stage' => AMPSyncStageOption::CREATE_ENTRIES));
                // End of stage.

            case AMPSyncStageOption::REMOVE_OLD_ENTRIES:
                $this->logger->info('Start of stage.', array('stage' => $stage));
                $this->continueExecution()->removeOldEntries();

                $stage = AMPSyncStageOption::DOWNLOAD_FILES;
                $this->ampSyncStageOption->updateValue($stage);
                $this->logger->info('End of stage.', array('stage' => $stage));
                // End of stage.

            case AMPSyncStageOption::DOWNLOAD_FILES:
                $this->logger->info('Start of stage.', array('stage' => $stage));
                try {
                    kses_remove_filters();
                    $this->continueExecution()->download();
                } finally {
                    kses_init();
                }

                if($this->isPendingFilesExists()) {
                    throw new PendingFilesException();
                }

                $this->markAsFinished();

                $stage = AMPSyncStageOption::OK;
                $this->ampSyncStageOption->updateValue($stage);
                $this->logger->info('End of stage.', array('stage' => AMPSyncStageOption::DOWNLOAD_FILES));
                // End of stage.

            case AMPSyncStageOption::OK:
                $this->logger->info('Start of stage.', array('stage' => $stage));
                $this->logger->info('End of stage.', array('stage' => AMPSyncStageOption::OK));
                break;
        }

        return $this;
    }

    /**
     * Mark sync complete.
     *
     * @return $this For chain calls.
     */
    protected function markAsFinished()
    {
        $this->logger->info('All AMP styles successfully synced.');
        $this->useAMPStylesOption->updateValue(true);

        $this->ampSyncFailureNoticeOption->delete();
        $this->ampSyncFailureOption->delete();
        $this->ampSyncLastFailureNameOption->delete();

        return $this;
    }

    /**
     * Mark that limit of downloads with errors exceeded.
     *
     * This means that we need to stop syncing.
     *
     * @return $this For chain calls.
     */
    protected function markAsLimitDownloadsExceed()
    {
        $this->logger->warning('Limit of attempts downloads exceeded.');
        $this->ampSyncAttemptsLimitFailureOption->updateValue(true);
        return $this;
    }

    /**
     * Reset sync process.
     *
     * @return $this For chain calls.
     */
    public function resetSync()
    {
        $this->logger->info('Resetting AMP sync.');
        $this->ampStylesIdOption->delete();
        $this->ampSyncAttemptsLimitFailureOption->delete();
        $this->ampSyncFailureNoticeOption->delete();
        $this->ampSyncFailureOption->delete();
        $this->ampSyncLastFailureNameOption->delete();
        $this->ampSyncStageOption->delete();

        return $this;
    }

    /**
     * Reset download attempts counters on all posts.
     *
     * @throws OutOfTimeException If current cron process obsolete and we need to break execution.
     * @throws PostException If post was not updated.
     *
     * @return $this For chain calls.
     */
    public function resetPreviousState()
    {
        $toSearch = array(
            'post_type' => array(SetkaPostTypes::AMP_COMMON, SetkaPostTypes::AMP_THEME, SetkaPostTypes::AMP_LAYOUT),
            'posts_per_page' => 5,
            'post_status' => array(
                // Almost all post statuses except PostStatuses::ARCHIVE
                PostStatuses::PUBLISH,
                PostStatuses::DRAFT,
                PostStatuses::PENDING,
                PostStatuses::FUTURE,
                PostStatuses::TRASH,
            ),
            'orderby' => 'ID',

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,
        );

        do {
            $query = new \WP_Query($toSearch);
            $this->continueExecution();

            $this->logger->debug('Made query for posts.');
            $context = array('ids' => array());

            while ($query->have_posts()) {
                $post             = $query->next_post();
                $context['ids'][] = $post->ID;

                $post->post_status = PostStatuses::ARCHIVE;
                $this->isPostSaved(wp_update_post($post));
                $this->attemptsToDownloadPostMeta->setPostId($post->ID)->delete();
            }

            $context['counter'] = count($context['ids']);

            $this->logger->debug('Found posts.', $context);

            $query->rewind_posts();
        } while ($query->have_posts());

        $this->logger->info('Counters was removed for all posts.');

        return $this;
    }

    /**
     * Remove older posts with AMP configs.
     *
     * @throws OutOfTimeException If current cron process obsolete and we need to break execution.
     *
     * @throws PostException If post was not deleted.
     *
     * @return $this For chain calls.
     */
    public function removePreviousAMPConfigs()
    {
        $toSearch = array(
            'post_type' => SetkaPostTypes::AMP_CONFIG,
            'posts_per_page' => 5,
            'post_status' => array(
                // Almost all post statuses except PostStatuses::ARCHIVE
                PostStatuses::PUBLISH,
                PostStatuses::DRAFT,
                PostStatuses::PENDING,
                PostStatuses::FUTURE,
                PostStatuses::TRASH,
            ),

            'date_query' => array(
                'before' => $this->lastAMPStylesPost->post_date_gmt,
                'inclusive' => false,
                'column' => 'post_date_gmt',
            ),

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,
        );

        do {
            $query = new \WP_Query($toSearch);
            $this->continueExecution();

            $this->logger->debug('Made query for posts with previous AMP configs.');
            $context = array('ids' => array());

            while ($query->have_posts()) {
                $post             = $query->next_post();
                $context['ids'][] = $post->ID;
                $this->isPostDeleted(wp_delete_post($post->ID), $post);
            }

            $context['counter'] = count($context['ids']);

            $this->logger->debug('Found posts with previous AMP configs was removed.', $context);

            $query->rewind_posts();
        } while ($query->have_posts());

        $this->logger->info('All posts with previous AMP configs was removed.');

        return $this;
    }

    /**
     * Create WordPress post entries and mark them as drafts.
     *
     * @throws PostException If post was not created.
     * @throws PostMetaException If post meta was not updated.
     * @throws OutOfTimeException If current cron process obsolete and we need to break execution.
     *
     * @return $this For chain calls.
     */
    public function create()
    {
        $filesSections = $this->ampStylesOption->get();
        foreach ($filesSections as $sectionName => $section) {
            try {
                $postType = SetkaPostTypes::getPostType($sectionName);
            } catch (\Exception $exception) {
                $this->logger->warning('Not supported section name.', array('section' => $sectionName));
                continue; // Supports only 3 types of section names.
            }

            foreach ($section as $fileIndex => $file) {
                $query = new \WP_Query(array(
                    'post_type' => $postType,
                    'post_status' => array(
                        // Long version of ANY post_status since WP doesn't support it as expected.
                        PostStatuses::PUBLISH,
                        PostStatuses::DRAFT,
                        PostStatuses::PENDING,
                        PostStatuses::FUTURE,
                        PostStatuses::TRASH,
                        PostStatuses::ARCHIVE,
                    ),
                    'name' => $file['id'],
                    // Don't save result into cache since this used only by cron.
                    'cache_results' => false,
                    'posts_per_page' => 1,
                ));

                $this->continueExecution();

                $post = $query->have_posts() ? $query->next_post() : null;

                $post = $this->createPostData($postType, $file, $post);
                $this->logger->debug('Post for file.', array('post_id' => $post->ID, 'post_name' => $post->post_name));

                $post                                             = $this->createOrUpdateEntry($post, $file);
                $filesSections[$sectionName][$fileIndex]['wp_id'] = $post->ID;
            }
        }
        $this->logger->info('Looped through all files.');
        $context                      = array();
        $context['amp_styles_update'] = $this->ampStylesOption->updateValue($filesSections);
        $context['amp_styles']        = $filesSections;
        $this->logger->debug('AMP styles updated.', $context);

        return $this;
    }

    /**
     * @param $postType string WordPress post type.
     * @param $file array {
     * An array with file details.
     *
     * @type $id string Unique id of theme or layout.
     * @type $url string File url.
     * @type $filetype string File type (always css).
     * }
     * @param $post null|\WP_Post Post or null.
     *
     * @return \WP_Post Will be used in wp_insert_post() method.
     */
    public function createPostData($postType, $file, \WP_Post $post = null)
    {
        if(!$post) {
            $post = new \WP_Post(new \stdClass()); // \WP_Post requires an object in constructor.
        }

        $post->post_type   = $postType;
        $post->post_status = PostStatuses::DRAFT;
        $post->post_name   = $file['id'];

        return $post;
    }

    /**
     * Creates or updates Post entry in WordPress.
     *
     * @param $post \WP_Post
     *
     * @throws PostException If post was not created.
     * @throws PostMetaException If post meta was not updated.
     *
     * @return \WP_Post
     */
    public function createOrUpdateEntry($post, $file)
    {
        $this->logger->debug('Start creating/updating post.');
        $postID = wp_insert_post($post);
        $this->logger->debug('Result of inserting post.', array('wp_insert_post' => $postID));
        $this->isPostSaved($postID);
        $post->ID = $postID;

        $this->originUrlPostMeta->setPostId($postID)->deleteLocal();

        if($file['url'] !== $this->originUrlPostMeta->get()) {
            $originUrlPostMetaResult = $this->originUrlPostMeta->updateValue($file['url']);
            $this->logger->debug('Origin URL post meta updated.', array('update_post_meta' => $originUrlPostMetaResult));
            $this->isPostMetaCreated($originUrlPostMetaResult);
        }

        $this->setkaFileTypePostMeta->setPostId($postID)->deleteLocal();

        if($file['filetype'] !== $this->setkaFileTypePostMeta->get()) {
            $setkaFileTypePostMetaResult = $this->setkaFileTypePostMeta->updateValue($file['filetype']);
            $this->logger->debug('File type post meta updated.', array('update_post_meta' => $setkaFileTypePostMetaResult));
            $this->isPostMetaCreated($setkaFileTypePostMetaResult);
        }

        $this->attemptsToDownloadPostMeta->setPostId($postID)->delete();

        return $post;
    }

    /**
     * Remove old AMP entries.
     *
     * @throws OutOfTimeException If current cron process obsolete and we need to break execution.
     * @throws PostException If post was not deleted.
     *
     * @return $this For chain calls.
     */
    public function removeOldEntries()
    {
        $toSearch = array(
            'post_type' => array(SetkaPostTypes::AMP_COMMON, SetkaPostTypes::AMP_THEME, SetkaPostTypes::AMP_LAYOUT),
            'posts_per_page' => 5,
            'post_status' => PostStatuses::ARCHIVE,
            'orderby' => 'ID',

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,
        );

        do {
            $query = new \WP_Query($toSearch);
            $this->continueExecution();

            $this->logger->debug('Made query for archived posts.');
            $context = array('ids' => array());

            while ($query->have_posts()) {
                $post             = $query->next_post();
                $context['ids'][] = $post->ID;

                $this->isPostDeleted(wp_delete_post($post->ID), $post);
            }

            $context['counter'] = count($context['ids']);

            $this->logger->debug('Found the following archived posts.', $context);

            $query->rewind_posts();
        } while ($query->have_posts());

        $this->logger->info('Archive entries was removed.');

        return $this;
    }

    /**
     * Download AMP files.
     *
     * @throws OutOfTimeException If current cron process obsolete and we need to break execution.
     * @throws PostException If post was not updated.
     * @throws PostMetaException If post meta for file not found.
     * @throws ReadFileException If file from temporary folder is not able for reading.
     */
    public function download()
    {
        do {
            try {
                $query = WPQueryFactory::createWhereAMPFileIsDraft();
                $this->continueExecution();

                if($query->have_posts()) {
                    $post = $query->next_post();

                    if(!$this->originUrlPostMeta->setPostId($post->ID)->isValid()) {
                        throw new PostMetaException();
                    }

                    $this->logger->debug('Start downloading file.', array('id' => $post->ID, 'url' => $this->originUrlPostMeta->get()));

                    try {
                        $path = $this->downloader->download($this->originUrlPostMeta->get())->getResult();

                        try {
                            $content = $this->fileSystem->getContents($this->downloader->getResult());
                        } catch (\Exception $exception) {
                            throw new ReadFileException($exception->getMessage(), $exception->getCode(), $exception);
                        }

                        $post->post_content = $content;
                        $post->post_status  = PostStatuses::PUBLISH;
                        $this->isPostSaved(wp_update_post($post));

                        $this->logger->debug('File successful downloaded and saved.');

                        $this->removeDownloadsCounter($post);
                    } catch (MaxFileSizeException $exception) {
                        $this->markPostPending($exception, $post);
                        break;
                    } catch (FileDownloadException $exception) {
                        $this->markPostPending($exception, $post);
                        break;
                    }
                }
            } finally {
                $query->rewind_posts(); // To make while condition workable.

                if(isset($path) && is_string($path) && $this->fileSystem->exists($path)) {
                    $this->fileSystem->unlink($path);
                }
            }
        } while ($query->have_posts());

        return $this;
    }

    /**
     * Mark post pending (means what post can not be downloaded).
     *
     * @param $exception \Exception Reason because of which post cannot be downloaded.
     * @param $post \WP_Post Post which cannot be downloaded.
     *
     * @throws PostMetaException If post meta was not updated.
     * @throws PostException If post was not updated.
     *
     * @return $this For chain calls.
     */
    protected function markPostPending($exception, $post)
    {
        $this->logger->warning('Error while file downloading.', array('exception' => get_class($exception)));
        $this->saveFailure($exception);
        $this->increaseDownloadsCounter($post);
        $post->post_status = PostStatuses::PENDING;
        $this->isPostSaved(wp_update_post($post));
        return $this;
    }

    /**
     * Increase post download attempts counter +1.
     *
     * @param $post \WP_Post Post which should be updated.
     *
     * @throws PostMetaException If post meta was not updated.
     *
     * @return $this For chain calls.
     */
    protected function increaseDownloadsCounter(\WP_Post $post)
    {
        $this->attemptsToDownloadPostMeta->setPostId($post->ID)->deleteLocal();
        $counter = (int) $this->attemptsToDownloadPostMeta->get();
        $counter++;
        $result = $this->attemptsToDownloadPostMeta->updateValue($counter);
        $this->isPostMetaCreated($result);
        return $this;
    }

    /**
     * Remove download counter.
     *
     * @param $post \WP_Post Post which should be updated.
     *
     * @return $this For chain calls.
     */
    protected function removeDownloadsCounter(\WP_Post $post)
    {
        $this->attemptsToDownloadPostMeta->setPostId($post->ID)->delete();
        return $this;
    }

    /**
     * Transfer pending files back to download queue.
     *
     * @throws OutOfTimeException If current cron process obsolete and we need to break execution.
     * @throws PostException If post was not updated.
     *
     * @return $this For chain calls.
     */
    public function checkPendingFiles()
    {
        do {
            $query = WPQueryFactory::createWhereAMPFileIsPending();
            $this->continueExecution();

            if($query->have_posts()) {
                $this->logger->info('There is pending files. Transfer it to drafts.');
                $post = $query->next_post();
                $this->logger->debug('Pending file.', array('id' => $post->ID));

                $this->attemptsToDownloadPostMeta->setPostId($post->ID)->deleteLocal();
                $attempts = (int) $this->attemptsToDownloadPostMeta->get();

                if($attempts < $this->downloadAttempts) {
                    $postID = wp_update_post(array(
                        'ID' => $post->ID,
                        'post_status' => PostStatuses::DRAFT,
                    ));
                    $this->isPostSaved($postID);
                } else {
                    $this->markAsLimitDownloadsExceed();
                    break;
                }

                $query->rewind_posts();
            }
        } while ($query->have_posts());

        return $this;
    }

    /**
     * Check if pending files exists.
     *
     * @return bool True if one or more pending files exists.
     */
    public function isPendingFilesExists()
    {
        $query = WPQueryFactory::createWhereAMPFileIsPending();
        if($query->have_posts()) {
            $this->logger->info('Pending files exist. The manager should be run again.');
            return true;
        }
        return false;
    }

    /**
     * Remove all AMP files from DB.
     *
     * @throws OutOfTimeException If current cron process obsolete and we need to break execution.
     *
     * @return $this For chain calls.
     */
    public function deleteAllFiles()
    {
        $toSearch = array(
            'post_type' => array(SetkaPostTypes::AMP_COMMON, SetkaPostTypes::AMP_THEME, SetkaPostTypes::AMP_LAYOUT),
            'posts_per_page' => 5,
            'post_status' => PostStatuses::ANY,
            'orderby' => 'ID',

            // Don't save result into cache since this used only by cron.
            'cache_results' => false,
        );

        do {
            $query = new \WP_Query($toSearch);
            $this->continueExecution();

            while ($query->have_posts()) {
                $post = $query->next_post();
                wp_delete_post($post->ID);
            }
            $query->rewind_posts();
        } while ($query->have_posts());
        return $this;
    }

    /**
     * Add new AMP config for future sync.
     *
     * @param $config array New AMP config which will be added into DB.
     *
     * @throws JsonEncodeException If wp_json_encode return bad result.
     * @throws PostException If post was not created.
     *
     * @return $this For chain calls.
     */
    public function addNewConfig(array $config)
    {
        $json = wp_json_encode($config);

        if(!is_string($json)) {
            throw new JsonEncodeException();
        }

        $post = wp_insert_post(
            array(
                'post_type' => SetkaPostTypes::AMP_CONFIG,
                'post_content' => $json,
                'post_status' => PostStatuses::PUBLISH,
            ),
            true
        );

        $this->isPostSaved($post);

        return $this;
    }

    /**
     * Return last created AMP config (based on post ID).
     *
     * @throws NoAMPConfigException If AMP configs weren't found.
     *
     * @return \WP_Post Last created AMP config.
     */
    public function findLastConfig()
    {
        $query = WPQueryFactory::createWhereLastAMPConfig();

        if($query->have_posts()) {
            return $query->next_post();
        }

        throw new NoAMPConfigException();
    }

    /**
     * Transform post_content into array with AMP config.
     *
     * @param \WP_Post $post Post with AMP config as JSON string.
     *
     * @throws JsonDecodeException If JSON decoding was failed.
     *
     * @return array AMP config.
     */
    public function transformConfig(\WP_Post $post)
    {
        $config = json_decode($post->post_content, true);

        if(is_array($config)) {
            return $config;
        }
        throw new JsonDecodeException(json_last_error_msg(), json_last_error());
    }

    /**
     * Returns callable which used to check process relevance.
     *
     * @return callable Something that will be called after each iteration to check
     * if current PHP process still not obsolete.
     */
    public function getContinueExecution()
    {
        return $this->continueExecution;
    }

    /**
     * Sets callable which used to check process relevance.
     *
     * @param callable $continueExecution Something that will be called after each iteration to check
     * if current PHP process still not obsolete.
     *
     * @return $this For chain calls.
     */
    public function setContinueExecution($continueExecution)
    {
        $this->continueExecution = $continueExecution;
        return $this;
    }

    /**
     * Checks should we break process or not.
     *
     * @throws OutOfTimeException In case if need to stop progress.
     *
     * @return $this For chain calls.
     */
    public function continueExecution()
    {
        $this->logger->debug('Checking continue execution.');
        call_user_func($this->getContinueExecution());
        return $this;
    }

    /**
     * Check if meta saved.
     *
     * @param $meta mixed Result of updating meta.
     *
     * @throws PostMetaException
     *
     * @return $this For chain calls.
     */
    protected function isPostMetaCreated($meta)
    {
        if((is_int($meta) && $meta > 0) || true === $meta) {
            return $this;
        }
        $this->logger->error('Post Meta was not saved.', array('result' => $meta));
        throw new PostMetaException();
    }

    /**
     * Check that post was created (or updated).
     *
     * @param $id int|\WP_Error Result of wp_update_post()
     * @throws PostException If post not updated (or created).
     *
     * @return $this For chain calls.
     */
    protected function isPostSaved($id)
    {
        if(!is_int($id) || !$id > 0) {
            $this->logger->error('Post was not saved.', array('result' => $id));
            throw new PostException();
        }
        return $this;
    }

    /**
     * Check if post was deleted.
     *
     * @param $result mixed Value returned by wp_delete_post.
     * @param $post \WP_Post Post which was tried to remove.
     *
     * @throws PostException
     *
     * @return $this
     */
    protected function isPostDeleted($result, \WP_Post $post)
    {
        if(is_a($result, \stdClass::class) || is_a($result, \WP_Post::class)) {
            return $this;
        }
        $this->logger->error('Post was not deleted.', array('id' => $post->ID, 'result' => $result));
        throw new PostException();
    }

    /**
     * Saves passed exception into site options.
     *
     * @param \Exception $exception
     *
     * @return $this For chain calls.
     */
    public function saveFailure(\Exception $exception)
    {
        $this->logger->error('Saving failure.', array('exception' => get_class($exception)));
        $this->ampSyncFailureOption->updateValue(true);
        $this->ampSyncLastFailureNameOption->updateValue(get_class($exception));
        return $this;
    }
}
