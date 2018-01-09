<?php
namespace Setka\Editor\Admin\Service\FilesManager;

use Setka\Editor\Admin\Cron\Tasks\Files\FilesManagerTask;
use Setka\Editor\Admin\Cron\Tasks\Files\FilesQueueTask;
use Setka\Editor\Admin\Cron\Tasks\Files\SendFilesStatTask;
use Setka\Editor\Admin\Options\Files\FileSyncFailureOption;
use Setka\Editor\Admin\Options\Files\FileSyncOption;
use Setka\Editor\Admin\Options\Files\FileSyncStageOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Service\EditorConfigGenerator\EditorConfigGeneratorFactory;
use Setka\Editor\Admin\Service\FilesCreator\FilesCreatorFactory;
use Setka\Editor\Admin\Service\FilesManager\Exceptions\DeletingAttemptsDownloadsMetaException;
use Setka\Editor\Admin\Service\FilesManager\Exceptions\FailureOptionException;
use Setka\Editor\Admin\Service\FilesManager\Exceptions\FlushingCacheException;
use Setka\Editor\Admin\Service\FilesManager\Exceptions\SyncDisabledByUseException;
use Setka\Editor\Admin\Service\FilesSync\Exceptions\AwaitPendingFilesException;
use Setka\Editor\Admin\Service\FilesSync\Exceptions\LimitDownloadingAttemptsException;
use Setka\Editor\Admin\Service\FilesSync\SynchronizerFactory;
use Setka\Editor\Admin\Service\WPQueryFactory;
use Setka\Editor\Entries\Meta\AttemptsToDownloadMeta;
use Setka\Editor\Entries\PostStatuses;
use Setka\Editor\Entries\SetkaEditorFilePostType;

class FilesManager {

	/**
	 * @var callable Callback which checked after each iteration in $this->syncFiles().
	 */
	protected $continueExecution;

    /**
     * @var FileSyncFailureOption
     */
	protected $fileSyncFailureOption;

    /**
     * @var FileSyncOption
     */
	protected $fileSyncOption;

    /**
     * @var FileSyncStageOption
     */
    protected $fileSyncStageOption;

    /**
     * @var UseLocalFilesOption
     */
    protected $useLocalFilesOption;

    /**
     * FilesManager constructor.
     *
     * @param callable $continueExecution
     * @param FileSyncFailureOption $fileSyncFailureOption
     * @param FileSyncOption $fileSyncOption
     * @param FileSyncStageOption $fileSyncStageOption
     * @param UseLocalFilesOption $useLocalFilesOption
     */
    public function __construct(
        $continueExecution,
        FileSyncFailureOption $fileSyncFailureOption,
        FileSyncOption $fileSyncOption,
        FileSyncStageOption $fileSyncStageOption,
        UseLocalFilesOption $useLocalFilesOption
    ) {
        $this->continueExecution = $continueExecution;
        $this->fileSyncFailureOption = $fileSyncFailureOption;
        $this->fileSyncOption = $fileSyncOption;
        $this->fileSyncStageOption = $fileSyncStageOption;
        $this->useLocalFilesOption = $useLocalFilesOption;
    }

    public function enableSyncingTasks() {
        $task = new FilesManagerTask();
        $task->register();

        $task = new FilesQueueTask();
        $task->register();

        return $this;
    }

    public function disableSyncingTasks() {
        $task = new FilesManagerTask();
        $task->unRegisterHook();

        $task = new FilesQueueTask();
        $task->unRegisterHook();

        $this->disableLocalUsage();

        return $this;
    }

    public function restartSyncing() {
        // Disable local usage
        $this->disableLocalUsage();

        // Reset failure flag
        $this->fileSyncFailureOption->delete();

        // Reset stage option
        $this->fileSyncStageOption->delete();

        return $this;
    }

    public function disableLocalUsage() {
        $this->useLocalFilesOption->delete();
        return $this;
    }

    protected function enableLocalUsage() {
        $this->useLocalFilesOption->updateValue('1');
        return $this;
    }

    public function run() {
        // Check if sync enabled?
        // Disable local usage if needed
        if('0' === $this->fileSyncOption->getValue())
            throw new SyncDisabledByUseException();

        if(defined('SETKA_EDITOR_SYNC_FILES') && false === SETKA_EDITOR_SYNC_FILES)
            throw new SyncDisabledByUseException();

        if('1' === $this->fileSyncFailureOption->getValue())
            throw new FailureOptionException();

        // Lets switch states.

        $stages = $this->fileSyncStageOption;
        $stage = $this->fileSyncStageOption->getValue();

        switch($stage) {
            // Download list of files // and update stage
            case $stages::DOWNLOAD_FILES_LIST:
            default:
                $this->continueExecution();

                try {
                    // Get and save new list of files.
                    $downloadListOfFiles = DownloadListOfFilesFactory::create();
                    $downloadListOfFiles->execute();

                    // Reset download counters.
                    $this->resetAllDownloadsCounters();
                } catch (\Exception $exception) {
                    throw $exception;
                } finally {
                    unset($downloadListOfFiles);
                }
                $this->fileSyncStageOption->updateValue($stages::CREATE_ENTRIES);
                $stage = $this->fileSyncStageOption->getValue();

                // END of this stage //

            // Create files in DB // and update stage
            case $stages::CREATE_ENTRIES;
                $this->continueExecution();

                try {
                    $filesCreator = FilesCreatorFactory::createFilesCreator();
                    $filesCreator->createPosts();
                } catch (\Exception $exception) {
                    throw $exception;
                } finally {
                    unset($filesCreator);
                }
                $this->fileSyncStageOption->updateValue($stages::DOWNLOAD_FILES);
                $stage = $this->fileSyncStageOption->getValue();

                // END of this stage //

            // Download files // and update stage
            case $stages::DOWNLOAD_FILES:
                $this->continueExecution();

                try {
                    $sync = SynchronizerFactory::create();
                    $sync->syncFiles();
                }
                catch (LimitDownloadingAttemptsException $exception) {
                    // Disable everything
                    $this->failureOnSyncing();

                    // Send stat
                    $sendFilesStatTask = new SendFilesStatTask();
                    $sendFilesStatTask->register();

                    throw $exception;
                }
                catch (AwaitPendingFilesException $exception) {
                    // Pending files wait for the next run
                    return $this;
                }
                catch (\Exception $exception) {
                    throw $exception;
                } finally {
                    unset($sync);
                }

                $this->fileSyncStageOption->updateValue($stages::GENERATE_EDITOR_CONFIG);
                $stage = $this->fileSyncStageOption->getValue();

                // END of this stage //

            // Generate JSON and switch to local usage // and update stage
            case $stages::GENERATE_EDITOR_CONFIG:
                $this->continueExecution();

                try {
                    $generator = EditorConfigGeneratorFactory::create();
                    $generator->generate();
                } catch (\Exception $exception) {
                    throw $exception;
                } finally {
                    unset($generator);
                }

                // Send stat
                $sendFilesStatTask = new SendFilesStatTask();
                $sendFilesStatTask->register();

                $this->fileSyncStageOption->updateValue('ok');
                $stage = $this->fileSyncStageOption->getValue();

                // END of this stage //

            case $stages::OK:
                // This means that local files usage enabled.
                // END of this stage //
                break;
        }

        return $this;
    }

    public function failureOnSyncing() {
        $this->fileSyncFailureOption->updateValue('1');
        return $this;
    }

	/**
	 * Loop over pending files and mark it as drafts.
	 *
	 * Sync process attempt download this files (drafts) again.
     *
     * @return $this For chain calls.
	 */
	public function checkPendingFiles() {
		do {
			$query = WPQueryFactory::createWhereFilesIsPending();

            $this->continueExecution();

			if($query->have_posts()) {
				$query->the_post();
				$post = get_post();

				$attemptsToDownloadMeta = new AttemptsToDownloadMeta();
				$attemptsToDownloadMeta->setPostId($post->ID);
				$attempts = (int)$attemptsToDownloadMeta->getValue();

				if($attempts < SETKA_EDITOR_FILES_DOWNLOADING_ATTEMPTS) {
					wp_update_post(array(
						'ID' => $post->ID,
						'post_status' => PostStatuses::DRAFT,
					));
				} else {
					// Disable sync.
                    $this->failureOnSyncing();
					// Stop the loop.
					break;
				}

				$query->rewind_posts();
			}
		} while ($query->have_posts());

		wp_reset_postdata(); // restore globals back

        return $this;
	}

	/**
	 * Mark all files in DB as archived.
	 *
	 * After this operation this files will no longer affects downloading queue.
	 *
	 * @return mixed Result of SQL request with $wpdb->query().
     *
     * @throws FlushingCacheException If cache flushing was failed.
	 */
	public function markAllFilesAsArchived() {
		global $wpdb;

		$query = "
		UPDATE {$wpdb->posts}
		SET
			post_status = %s
		WHERE
			post_type = %s
		";

		$query = $wpdb->prepare(
			$query,
			PostStatuses::ARCHIVE,
			SetkaEditorFilePostType::NAME
		);

		$queryResult = $wpdb->query($query);

        // Reset cache
        $result = wp_cache_flush();

        // Different flushing mechanisms working different.
        // For example Memcached returns null as successful result.
        if(false === $result)
            throw new FlushingCacheException();

        return $queryResult;
	}

	/**
	 * Completely remove downloads counters from post meta for all posts.
	 *
	 * And also resetting object cache.
	 *
	 * @return $this For chain calls.
	 *
	 * @throws FlushingCacheException If can't reset the object cache.
	 * @throws DeletingAttemptsDownloadsMetaException If can't delete post metas from DB.
	 */
	public function resetAllDownloadsCounters() {
		// Reset cache
		$result = wp_cache_flush();

		// Different flushing mechanisms working different.
		// For example Memcached returns null as successful result.
		if($result === false)
			throw new FlushingCacheException();

		unset($result);
		global $wpdb;

		// Remove post metas from table
		$attemptsDownloadsMeta = new AttemptsToDownloadMeta();
		$query = "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s";
		$query = $wpdb->prepare($query, $attemptsDownloadsMeta->getName());

		$result = $wpdb->query($query);

		if(!is_numeric($result))
			throw new DeletingAttemptsDownloadsMetaException();

		return $this;
	}

	public function getFilesStat() {
        global $wpdb;

        $stat = array(
            PostStatuses::ANY     => 0,
            PostStatuses::ARCHIVE => 0,
            PostStatuses::DRAFT   => 0,
            PostStatuses::PUBLISH => 0,
            PostStatuses::TRASH   => 0,
            PostStatuses::FUTURE  => 0,
            PostStatuses::PENDING => 0,
        );

        $query =
            "SELECT
				post_status AS status,
				COUNT(ID) AS counter
    		FROM {$wpdb->posts}
    		WHERE post_type = %s
    		GROUP BY post_status";

        $query = $wpdb->prepare($query, SetkaEditorFilePostType::NAME);
        $results = $wpdb->get_results($query);

        if(is_array($results)) {
            foreach($results as $result) {
                $stat[$result->status] = (int)$result->counter;
            }
        }
        unset($results, $result);

        $query =
            "SELECT COUNT(*) as amount
    		FROM {$wpdb->posts}
    		WHERE post_type = %s";

        $query = $wpdb->prepare($query, SetkaEditorFilePostType::NAME);
        $results = $wpdb->get_results($query);

        if(is_array($results)) {
            $stat[PostStatuses::ANY] = (int)$results[0]->amount;
        }

        return $stat;
    }

	/**
	 * @return callable
	 */
	public function getContinueExecution() {
		return $this->continueExecution;
	}

	/**
	 * @param callable $continueExecution
	 *
	 * @return $this For chain calls.
	 */
	public function setContinueExecution($continueExecution) {
		$this->continueExecution = $continueExecution;
		return $this;
	}

	public function continueExecution() {
        return call_user_func($this->getContinueExecution());
    }
}
